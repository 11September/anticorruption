<?php

namespace App\Http\Controllers;

use PDF;
use App;
use Excel;
use App\Page;
use App\Object;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    
    public function page($page)
    {
        $page = Page::select('body', 'title', 'meta_description', 'meta_keywords')->where('status', 'ACTIVE')->where('slug', $page)->first();

        $suma = Object::sumaRepairsAll();

        $suma = $suma['0'];

        return view('page', compact('page', 'suma'));
    }

//    public function api()
//    {
//        $objects = Object::published()->get();
//
//        $ids = array();
//        $ids = $objects->pluck('id');
//        $ids->toArray();
//
//        $suma = Object::sumaRepairsAll();
//
//        $suma = $suma['0'];
//
//        return view('api' ,compact('suma'));
//    }

//    public function instruction()
//    {
//        $objects = Object::published()->get();
//
//        $ids = array();
//        $ids = $objects->pluck('id');
//        $ids->toArray();
//
//        $suma = Object::sumaRepairs($ids);
//
//        $suma = Object::sumaRepairsAll();
//
//        return view('instruction' ,compact('suma'));
//    }

    public function addtionalDownload($path)
    {
        return response()->download(public_path().'/storage/documents/'.$path, $path);
    }

    public function exportDownload(Request $request)
    {

    	$this->validate($request, [
	        'save' => 'required',
	    ]);

        if($request->input('download') == 'pdf'){
            $tableData = '';

        	$pdf = App::make('dompdf.wrapper');			

            $objectNumber = 1;
            foreach ($request->input('save') as $objectId) {
                $object = Object::where('id', '=', $objectId)->with(['city', 'category', 'customer', 'contractor'])->get()->toArray();
                $object = $object[0];

                $tableData .=
                    "
                    <tr>
                        <td>".$objectNumber."</td>
                        <td>".$object['name']."</td>
                        <td>".$object['address']."</td> 
                        <td>".$object['city']['name']."</td>
                        <td>".$object['category']['name']."</td>
                        <td>".$object['customer']['name']."</td>
                        <td>".$object['contractor']['name']."</td>
                        <td>".$object['price']."</td>
                        <td>".$object['work_description']."</td>
                        <td>".$object['finished_at']."</td>
                    </tr>";

                $objectNumber++;
            }

            $pdf->loadHTML('
                <head>
                    <style>
                        table { border-collapse: collapse; border: 2px solid black;}
                        th {border: 1px solid black; padding: 7px; font-size: 12px;}
                        td {border: 1px solid black; padding: 7px; font-size: 12px;}
                        body { font-family: DejaVu Sans, sans-serif; }
                    </style>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                </head>
                <body>
                    <table style="width:100%">
                        <tr>
                            <th>№</th>
                            <th>Назва об\'єкту</th> 
                            <th>Адреса</th>
                            <th>Місто</th>
                            <th>Категорія</th>
                            <th>Замовник</th>
                            <th>Підрядник</th>
                            <th>Ціна, грн</th>
                            <th>Перелік робіт</th>
                            <th>Дата завершення робіт</th>
                        </tr>'
                        .$tableData.  
                    '</table>
                <body>
            ')->setPaper('a4', 'landscape');
                
			return $pdf->download('repairsExport.pdf');
        }

        elseif ($request->input('download') == 'word') {
        	$objectArr = [];
        	$phpWord = new \PhpOffice\PhpWord\PhpWord();
        	$section = $phpWord->addSection(['orientation' => 'landscape', 'marginLeft' => 395]);
			$header = array('size' => 10, 'bold' => true);

            $tableStyle = array(
                'borderColor' => '006699',
                'borderSize'  => 6,
                'cellMargin'  => 50
            );

			$table = $section->addTable($tableStyle);

            $table->addRow();

            $table->addCell(550)->addText('№');
            $table->addCell(1300)->addText('Назва об\'єкту');     
            $table->addCell(1500)->addText('Адреса');
            $table->addCell(1200)->addText('Місто');
            $table->addCell(1200)->addText('Категорія');
            $table->addCell(1200)->addText('Замовник');
            $table->addCell(1200)->addText('Підрядник');
            $table->addCell(900)->addText('Ціна, грн');
            $table->addCell(1700)->addText('Перелік робіт');
            $table->addCell(740)->addText('Дата завершення робіт');

            $objectNumber = 1;
			foreach ($request->input('save') as $objectId) {//IF FIELD NON EXISTS PUT NULL
        		$object = Object::where('id', '=', $objectId)->with(['city', 'category', 'customer', 'contractor'])->get()->toArray();

        		$object[0]['city_id'] = $object[0]['city']['name'];
        		$object[0]['category_id'] = $object[0]['category']['name'];
        		$object[0]['customer_id'] = $object[0]['customer']['name'];
        		$object[0]['contractor_id'] = $object[0]['contractor']['name'];
        		unset($object[0]['city'], $object[0]['category'], $object[0]['customer'], $object[0]['contractor'], $object[0]['id'], $object[0]['region_id'], 
                      $object[0]['status'], $object[0]['description'], $object[0]['additional_info'], $object[0]['maps_lat'], $object[0]['maps_lng'], 
                      $object[0]['finished_year'], $object[0]['created_at'], $object[0]['updated_at']);

        		$table->addRow();

                $table->addCell(550)->addText($objectNumber);
				$table->addCell(1300)->addText($object[0]['name']);		
				$table->addCell(1500)->addText($object[0]['address']);
				$table->addCell(1200)->addText($object[0]['city_id']);
				$table->addCell(1200)->addText($object[0]['category_id']);
				$table->addCell(1200)->addText($object[0]['customer_id']);
				$table->addCell(1200)->addText($object[0]['contractor_id']);
				$table->addCell(900)->addText($object[0]['price']);
				$table->addCell(1700)->addText($object[0]['work_description']);
				$table->addCell(740)->addText($object[0]['finished_at']);

                $objectNumber++;
        	}
        	$localFileName = md5(time()).'.docx';
        	$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
			$objWriter->save($localFileName);
            
			return response()->download(public_path().'/'.$localFileName, 'repairsExport.docx')->deleteFileAfterSend(true);
        }

        elseif ($request->input('download') == 'xls' || $request->input('download') == 'csv') {
        	$objectArr = [];

            $objectNumber = 1;
        	foreach ($request->input('save') as $objectId) {//IF FIELD NON EXISTS PUT NULL
        		$object = Object::where('id', '=', $objectId)->with(['city', 'category', 'customer', 'contractor'])->get()->toArray();

        		$object[0]['city_id'] = $object[0]['city']['name'];
        		$object[0]['category_id'] = $object[0]['category']['name'];
        		$object[0]['customer_id'] = $object[0]['customer']['name'];
        		$object[0]['contractor_id'] = $object[0]['contractor']['name'];

        		unset($object[0]['city'], $object[0]['category'], $object[0]['customer'], $object[0]['contractor'], $object[0]['id'], $object[0]['region_id'], 
                      $object[0]['status'], $object[0]['description'], $object[0]['additional_info'], $object[0]['maps_lat'], $object[0]['maps_lng'], 
                      $object[0]['finished_year'], $object[0]['created_at'], $object[0]['updated_at']);

                $object[0] = array('№' => $objectNumber) + $object[0];

				array_push($objectArr, $object[0]);

                $objectNumber++;
        	}

			return Excel::create('repairsExport', function($excel) use ($objectArr) {
				$excel->sheet('main', function($sheet) use ($objectArr)
		        {
					$sheet->fromArray($objectArr);
                    $sheet->cell('A1', function($cell) {
                        $cell->setValue('№');
                    });
                    $sheet->cell('B1', function($cell) {
                        $cell->setValue('Назва об\'єкту');
                    });
                    $sheet->cell('C1', function($cell) {
                        $cell->setValue('Адреса');
                    });
					$sheet->cell('D1', function($cell) {
				    	$cell->setValue('Місто');
					});
					$sheet->cell('E1', function($cell) {
				    	$cell->setValue('Категорія');
					});
					$sheet->cell('F1', function($cell) {
				    	$cell->setValue('Замовник');
					});
					$sheet->cell('G1', function($cell) {
				    	$cell->setValue('Підрядник');
					});
                    $sheet->cell('H1', function($cell) {
                        $cell->setValue('Ціна, грн');
                    });
                    $sheet->cell('I1', function($cell) {
                        $cell->setValue('Перелік робіт');
                    });
                    $sheet->cell('J1', function($cell) {
                        $cell->setValue('Дата завершення робіт');
                    });
                    
		        });
			})->download($request->input('download'));
        }
        else{
        	$data = 'error';
        }
    }
}
