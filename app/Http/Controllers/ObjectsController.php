<?php

namespace App\Http\Controllers;

use Excel;
use App\City;
use App\Region;
use App\Object;
use App\Finance;
use App\Customer;
use App\Document;
use Carbon\Carbon;
use App\Contractor;
use App\ObjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use App\Http\Requests\ObjectsFilterRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use \Exception;

class ObjectsController extends Controller
{
    public function index($id = "")
    {   

        if( $id ){
            $facebookObject = Object::findOrFail($id);
        }

        $objects = Object::selected()->with(
            array(
                'category' => function ($query) {
                    $query->select('id', 'name', 'image');
                },
                'region' => function ($query) {
                    $query->select('id', 'map_lat', 'map_lng');
                },
            ))->get();

        $filteredByCity = 'false';

        $ids = array();
        $ids = $objects->pluck('id');
        $ids->toArray();

        $suma = Object::sumaRepairs($ids);

        $regions = collect(Object::distinct('region_id')->pluck('region_id'));
        $regionContainsObjectsAmount = [];
        $regionClustersCoords = [];

        foreach ($regions as $regionId) {
            $regionContainsObjectsAmount[$regionId] = Object::where( "region_id", "=", $regionId )->whereNotNull('maps_lat')->whereNotNull('maps_lng')->count();
            $regionClustersCoords[$regionId] = Region::select('map_lat','map_lng')->where('id', $regionId)->get()->toArray();
        }

        $regionClustersCoords = collect( $regionClustersCoords );
        $regionContainsObjectsAmount = collect( $regionContainsObjectsAmount );
        // $regionContainsObjectsAmount = collect(Region::countRelatedObjects($objects));

        return view('welcome', compact('objects', 'suma', 'regionContainsObjectsAmount', 'regionClustersCoords', 'filteredByCity', 'facebookObject'));
    }

    public function filter(Request $request)
    {
        $objectsBuilder = Object::filter($request->all())->with('category', 'region', 'finances');
        $objects = $objectsBuilder->get();

        $filteredByCity = $request->city_id ? 'true' : 'false';

        $ids = array();
        $ids = $objects->pluck('id');
        $ids->toArray();

        $suma = Object::sumaRepairs($ids);

        $regions = collect(Object::distinct('region_id')->pluck('region_id'));
        $regionContainsObjectsAmount = [];
        $regionClustersCoords = [];

        foreach ($regions as $regionId) {
            $count = $objectsBuilder->where( "region_id", "=", $regionId )->whereNotNull('maps_lat')->whereNotNull('maps_lng')->count();
            if( $count > 0 ){
                $regionContainsObjectsAmount[$regionId] = $count;
                $regionClustersCoords[$regionId] = Region::select('map_lat','map_lng')->where('id', $regionId)->get()->toArray();
            }
            
        }

        $regionClustersCoords = collect( $regionClustersCoords );
        $regionContainsObjectsAmount = collect( $regionContainsObjectsAmount );

        return view('welcome', compact('objects', 'suma', 'regionContainsObjectsAmount', 'filteredByCity', 'regionClustersCoords'));
    }

    public function show(Request $request)
    {
        $object = Object::where('id', $request->id)->with('customer', 'contractor', 'city', 'documents', 'finances')->get();

        return response()->json($object);
    }

    public function importObjectsDatabase(Request $request)
    {
        try {

            $mapKeysCsv = ['category',
                'name',
                'address',
                'repair_price',
                'work_list',
                'work_date_completion',
                'customer',
                'EDRPOU_customer',
                'contractor',
                'EDRPOU_contractor',
                'additional_documents_(name)',
                'additional_documents_(link)',
                'city',
                'region',
                'latitude',
                'longitude',
                'object_date_creation'];

            $importData = [];
            $keys = [];
            $objectData = [];
            $rowData = [];
            $object = [];
            $queryLimitExceptionAdreses = '';
            $queryZeroExceptionAdreses = '';
            $googleApiLimitException = false;

            $validator = Validator::make($request->all(), [
                "file" => "required|mimes:csv,txt",
            ],
                [
                    "required" => "Оберіть файл.",
                    "mimes" => "Файл повинен бути формату CSV."
                ]);

            if ($validator->fails()) {
                $alerMessage = '';
                foreach ($validator->messages()->all() as $message) {
                    $alerMessage = ' ' . $alerMessage . $message . ' ';
                }
                throw new Exception($alerMessage);
            }

            $path = $request->file->storeAs('import', 'import.csv');

            if (($handle = fopen(storage_path() . DIRECTORY_SEPARATOR . "/app" . DIRECTORY_SEPARATOR . $path, 'r')) !== FALSE) {

                $row = 0;

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {

                    $num = count($data);
                    $row++;

                    if ($row == 1) {

                        foreach ($data as $value) {
                            // $value = iconv(mb_detect_encoding($value, mb_detect_order(), true), "UTF-8", $value);
                            array_push($keys, str_replace(' ', '_', $value));
                        }

                    } else {
                        $rowData = [];
                        foreach ($data as $value) {
                            // $value = iconv(mb_detect_encoding($value, mb_detect_order(), true), "UTF-8", $value);
                            array_push($rowData, str_replace('i', 'і', $value));
                        }
                        $objectData = array_combine($keys, $rowData);
                    }

                    if (!empty($objectData)) {
                        array_push($importData, $objectData);
                    }

                }

                fclose($handle);
            }

            $oldCityName = '';

            //$objects = Object::all();
            // $a = Object::where('id', 563)->get();
            // dump($a[0]->name);
            // dump('ДИТЯЧА КЛIНIЧНА ЛІКАРНЯ №4');
            // var_dump($a[0]->name);
            // var_dump('ДИТЯЧА КЛIНIЧНА ЛІКАРНЯ №4');
            // dump($a[0]->name === 'ДИТЯЧА КЛIНIЧНА ЛІКАРНЯ №4');
            // dd($a);
            foreach ($importData as $key => $objectData) {

                foreach ($mapKeysCsv as $exploredKey) {
                    if (!array_key_exists($exploredKey, $objectData)) {
                        throw new Exception('Ключ "' . $exploredKey . '" не валідна у CSV перевірте імпортований файл та його кодування');
                    }
                }

                $importData[$key]['updateObject'] = '';

                $exsistObject = Object::where( 'address', '=', $objectData['address'] )->with('category')->first();
                
                if( !is_null( $exsistObject ) ){

                    if( $exsistObject->name == $objectData['name'] ){

                        $importData[$key]['updateObject'] = $exsistObject->id;

                    }elseif( $exsistObject->name !=  $objectData['name'] && !is_null($exsistObject->category) ){

                        $importData[$key]['updateObject'] = $exsistObject->category->name == $objectData['category'] ? $exsistObject->id : '';

                    }
                }
            }
            // dd($importData);
            $regions = Region::all();

            for ($arrIndex = 0; $arrIndex < count($importData); $arrIndex++) {

                $googleApiZeroResultException = false;

                $objectData = $importData[$arrIndex];

                if (strlen($objectData['updateObject']) == 0) {
                    $object = new Object();
                }

                if (strlen($objectData['updateObject']) > 0) {
                    $object = Object::where('id', $objectData['updateObject'])->first();
                }

                $newObjectName = '';
                $newObjectAddress = '';
                $newObjectCity_id = 0;
                $newObjectCategory_id = 0;
                $newObjectCustomer_id = 0;
                $newObjectContractor_id = 0;
                $newObjectRegion_id = 0;
                $newObjectWork_description = '';
                $newObjectFinished_at = '';
                $newObjectFinished_year = '';
                $newObjectMaps_lat = '';
                $newObjectMaps_lng = '';
                $newDocumentTitle = '';
                $newDocumentFile_path = '';
                $newFinanceSuma = '';
                $newFinanceStatus = '';
                $newFinanceDescription = '';
                $newFinanceDate = '';
                $financeSuma = '';
                $newObjectCreateDate = Carbon::now();

                if ($object->address !== $objectData['address'] && strlen($object->lat) < 2 && strlen($object->lng) < 2) {

                    if (strlen($objectData['latitude']) < 2 || strlen($objectData['longitude']) < 2) {

                        $addressForRequest = str_replace(' ', '+', $importData[$arrIndex]['address']);
                        $json = file_get_contents('https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . $addressForRequest . '&key=AIzaSyAzxY-YzRtwoQs6_Q9hHk2ZSo2sWtIIFGM&language=uk');
                        $objectDataAPI = json_decode($json);
                        //mykey               AIzaSyAECoGPJKuBmmc4_Y0PjKkWRLUjheLqwAI
                        //rightkey            AIzaSyCylzj30nQuaMwhN6Xeqf7wrSYV7KR0yFs
                        //palces libraty key  AIzaSyAzxY-YzRtwoQs6_Q9hHk2ZSo2sWtIIFGM
                    }
                } else {
                    $objectDataAPI = 'COORDINATES_EXIST';
                }

                if (property_exists($objectDataAPI, 'status')) {
                    if ($objectDataAPI->status == 'ZERO_RESULTS') {
                        $googleApiZeroResultException = true;
                    }
                }

                foreach ($objectData as $key => $value) {

                    switch ($key) {

                        case 'category':

                            $checkCategory = ObjectCategory::where( 'name', '=', mb_strtolower( $value ) )->first();
                            
                            if( !is_null($checkCategory) ){
                                $category = $checkCategory;
                                $newObjectCategory_id = $category->id;
                            }else{
                                $newCategory = new ObjectCategory();
                                $newCategory->name = $value;
                                $newCategory->image = '';
                                $newCategory->save();
                                $newObjectCategory_id = $category->id;
                            }

                            break;

                        case 'name':

                            $newObjectName = $value;

                            break;

                        case 'address':

                            $newObjectAddress = $value;
                            $addressForRequest = str_replace(' ', '+', $value);

                            break;

                        case 'repair_price':

                            if (strlen($value) > 0) {
                                $newFinanceSuma = $value;
                                $newFinanceStatus = 'provided';
                                $newFinanceDescription = '';
                                $newFinanceDate = $objectData['object_date_creation'];
                            }

                            break;

                        case 'work_list':

                            $newObjectWork_description = $value;

                            break;

                        case 'work_date_completion':

                            $parsedYear = explode("-", $value);
                            $newObjectFinished_year = $parsedYear[0];

                            $newObjectFinished_at = $value;

                            if (strlen($value) <= 4 && strlen($value) !== 0) {
                                $newObjectFinished_at = $value . '-01';
                            }

                            break;

                        case 'customer':

                            $checkCustomer = Customer::where( 'name', '=', $value )->first();
                            
                            if( !is_null($checkCustomer) ){
                                $customer = $checkCustomer;
                                $newObjectCustomer_id = $customer->id;
                            }else{
                                $newCustomer = new Customer();
                                $newCustomer->name = $value;
                                $newCustomer->identification_customer = $importData[$arrIndex]['EDRPOU_customer'];
                                $newCustomer->save();
                                $newCustomer = $newCustomer->id;
                            }

                            break;

                        case 'EDRPOU_customer':

                            break;

                        case 'contractor':

                            $checkContractor = Contractor::where( 'name', '=', $value )->first();
                            
                            if( !is_null($checkContractor) ){
                                $contractor = $checkContractor;
                                $newObjectContractor_id = $contractor->id;
                            }else{
                                $newContractor = new Contractor();
                                $newContractor->name = $value;
                                $newContractor->identification_contractor = $importData[$arrIndex]['EDRPOU_contractor'];
                                $newContractor->save();
                                $newContractor = $newContractor->id;
                            }

                            break;

                        case 'EDRPOU_contractor':

                            break;

                        case 'additional_documents_(link)':

                            if (strlen($value) > 0) {
                                $newDocumentTitle = $value;
                                $newDocumentFile_path = $importData[$arrIndex]['additional_documents_(link)'];
                            }
                            break;

                        case 'additional_documents_(link)':

                            break;

                        case 'city':

                            $checkCity = City::where( 'name', '=', $value )->first();
                            
                            if( !is_null($checkCity) ){
                                $city = $checkCity;
                                $newObjectCity_id = $city->id;
                                $apiCityName = $city->name;
                            }else{
                                $newCity = new City();
                                $newCity->name = $value;
                                $newCity->save();
                                $newObjectCity_id = $newCity->id;
                                $apiCityName = $city->name;
                            }

                            break;

                        case 'latitude':

                            if (strlen($value) > 0) {

                                $newObjectMaps_lat = $value;
                                $newObjectMaps_lng = $objectData['longitude'];

                            } elseif ($objectDataAPI == 'COORDINATES_EXIST') {

                            } elseif ($objectDataAPI->status == 'OVER_QUERY_LIMIT') {

                                $googleApiLimitException = true;
                                $newObjectMaps_lng = '';
                                $newObjectMaps_lat = '';
                                $queryLimitExceptionAdreses .= " " . $objectData['address'];

                            } elseif ($googleApiZeroResultException) {

                                $googleApiZeroResultException = true;
                                $queryZeroExceptionAdreses .= " " . $objectData['address'];
                                $newObjectMaps_lng = '';
                                $newObjectMaps_lat = '';

                            } else {
                                dump($objectDataAPI);
                                $newObjectMaps_lat = $objectDataAPI->results[0]->geometry->location->lat;
                                $newObjectMaps_lng = $objectDataAPI->results[0]->geometry->location->lng;

                            }

                            break;

                        case 'longitude':

                            break;

                        case 'region':

                            foreach ($regions as $region) {
                                if ($region->name_ua == $value || $region->name_ru == $value) {
                                    $newObjectRegion_id = $region->id;
                                }
                            }

                            break;

                        case 'object_date_creation':

                            $newObjectCreateDate = $value;

                            break;
                    }
                }

                if (!$googleApiZeroResultException) {

                    $checkObject = Object::where( 'address', '=', $newObjectAddress )->with('category')->first();

                    if( !is_null( $checkObject ) ){

                        if( $checkObject->name == $objectData['name'] ){
                            $objectData['updateObject'] = $checkObject->id;
                        }

                        if( $checkObject->name !=  $objectData['name'] && !is_null($checkObject->category) ){
                            $objectData['updateObject'] = $checkObject->category->name == $objectData['category'] ? $checkObject->id : '';
                        }

                        $object = $checkObject;
                    }

                    $createNewFinance = true;

                    if (strpos($object->work_description, $newObjectWork_description) !== false) {
                        $newObjectWork_description = '';
                        $financeSuma = $newFinanceSuma;
                        $newFinanceSuma = 0;
                        $createNewFinance = false;
                    }

                    $object->name = $newObjectName;
                    $object->address = $newObjectAddress;
                    $object->city_id = $newObjectCity_id;
                    $object->category_id = $newObjectCategory_id;
                    $object->customer_id = $newObjectCustomer_id;
                    $object->contractor_id = $newObjectContractor_id;
                    $object->region_id = $newObjectRegion_id;
                    $object->price = strlen($objectData['updateObject']) > 0 ? (float)$newFinanceSuma + (float)$object->price : $newFinanceSuma;
                    $object->work_description = strlen($objectData['updateObject']) > 0 ? $object->work_description . ' ' . $newObjectWork_description : $newObjectWork_description;
                    $object->finished_at = $newObjectFinished_at;
                    $object->finished_year = $newObjectFinished_year;

                    if (is_numeric($newObjectMaps_lat)) {
                        $object->maps_lat = $newObjectMaps_lat;
                    }

                    if (is_numeric($newObjectMaps_lng)) {
                        $object->maps_lng = $newObjectMaps_lng;
                    }
                    
                    $object->save();

                    if (strlen($newDocumentTitle) > 0 && strlen($newDocumentFile_path) > 0) {
                        $checkDocument = Document::where([
                            ['object_id', '=', $object->id],
                            ['title', '=', $newObjectName],
                            ['file_path', '=', $newDocumentFile_path]
                        ])->first();

                        if ( !is_null($checkDocument) ) {
                            $newDocument = new Document();
                            $newDocument->title = $newObjectName;
                            $newDocument->file_path = $newDocumentFile_path;
                            $newDocument->object_id = $object->id;
                            $newDocument->save();
                        }else{
                            $newDocument = $checkDocument;

                            $newDocument->title = $newObjectName;
                            $newDocument->file_path = $newDocumentFile_path;
                            $newFinance->object_id = $object->id;
                            $newFinance->save();
                        }
                    }

                    if( strlen($newFinanceSuma) > 0 && $createNewFinance){
                        $checkFinance = Finance::where([
                            ['object_id', '=', $object->id],
                            ['description', '=', $newObjectWork_description]
                        ])->get();

                        if( !is_null( $checkFinance ) ){
                            $newFinance = new Finance();
                            
                            $newFinance->suma = $newFinanceSuma;
                            $newFinance->status = 'paid';
                            $newFinance->description = $newObjectWork_description;
                            $newFinance->date = $newFinanceDate;
                            $newFinance->object_id = $object->id;
                            $newFinance->save();
                        }else{
                            $newFinance = $checkFinance;

                            $newFinance->suma = $newFinanceSuma;
                            $newFinance->status = 'paid';
                            $newFinance->description = 'ДОПЛАТА ЗА: ' . $newFinance->description;
                            $newFinance->date = $newFinanceDate;
                            $newFinance->object_id = $object->id;
                            $newFinance->save();
                        }
                    }
                }
            }
            
            Storage::delete('/import/import.csv');
            // dd('yeee');
        } catch (Exception $exception) {
            return redirect()->back()->with([
                'message' => 'Не вдалося імпортувати інформацію (перевірте CSV файл)' . $exception->getMessage() . ': ' . $exception->getLine(),
                'alert-type' => 'error',
            ]);
        }

        $alertType = 'success';

        $googleApiLimitMessage = $googleApiLimitException ? ' Перевищено ліміт запитів до Google API деякі об\'єкти імпортовані без кооринат' : '';
        $googleApiZeroResultMessage = strlen($queryZeroExceptionAdreses) > 0 ? ' Google API не знайшло об\'єкти: ' : '';

        if (strlen($queryZeroExceptionAdreses) > 0 || $googleApiLimitException) {
            $alertType = 'warning';
        }

        return back()->with([
            'message' => 'Данні успішно імпортовані/оновлені' . $googleApiLimitMessage . $queryLimitExceptionAdreses . $googleApiZeroResultMessage . $queryZeroExceptionAdreses,
            'alert-type' => $alertType,
        ]);
        die();
    }

    public function exportDownloadAdmin()
    {
        $objectArr = [];
        $formattedArr = [];

        $objects = Object::with(['city', 'category', 'customer', 'contractor', 'region'])->get()->toArray();

        foreach ($objects as $object) {

            $document = Document::where('object_id', '=', $object['id'])->orderBy('created_at', 'desc')->first();

            $docTitle = $document['title'];
            $docPath = $document['file_path'];

            if ($document == null) {
                $docTitle = '';
                $docPath = '';
            };

            $object['city_id'] = $object['city']['name'];
            $object['category_id'] = $object['category']['name'];
            $object['customer_id'] = $object['customer']['name'];
            $object['contractor_id'] = $object['contractor']['name'];
            $object['region_id'] = $object['region']['name_ua'];

            $formattedArr = [
                $object['category']['name'],
                $object['name'],
                $object['address'],
                $object['price'],
                $object['work_description'],
                $object['finished_at'],
                $object['customer']['name'],
                $object['customer']['identification_customer'],
                $object['contractor']['name'],
                $object['contractor']['identification_contractor'],
                $docTitle,
                $docPath,
                $object['city']['name'],
                $object['region']['name_ua'],
                $object['maps_lat'],
                $object['maps_lng'],
                $object['created_at'],
            ];

            array_push($objectArr, $formattedArr);
        }

        return Excel::create('repairsExportDB', function ($excel) use ($objectArr) {
            $excel->sheet('main', function ($sheet) use ($objectArr) {
                $sheet->fromArray($objectArr);
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('категорія');
                });
                $sheet->cell('B1', function ($cell) {
                    $cell->setValue('назва');
                });
                $sheet->cell('C1', function ($cell) {
                    $cell->setValue('адреса');
                });
                $sheet->cell('D1', function ($cell) {
                    $cell->setValue('вартість ремонту');
                });
                $sheet->cell('E1', function ($cell) {
                    $cell->setValue('перелік робіт');
                });
                $sheet->cell('F1', function ($cell) {
                    $cell->setValue('дата завершення робіт');
                });
                $sheet->cell('G1', function ($cell) {
                    $cell->setValue('замовник');
                });
                $sheet->cell('H1', function ($cell) {
                    $cell->setValue('ЄДРПОУ замовника');
                });
                $sheet->cell('I1', function ($cell) {
                    $cell->setValue('підрядник');
                });
                $sheet->cell('J1', function ($cell) {
                    $cell->setValue('ЄДРПОУ підрядника');
                });
                $sheet->cell('K1', function ($cell) {
                    $cell->setValue('додаткові документи (назва)');
                });
                $sheet->cell('L1', function ($cell) {
                    $cell->setValue('додаткові документи (посилання)');
                });
                $sheet->cell('M1', function ($cell) {
                    $cell->setValue('місто');
                });
                $sheet->cell('N1', function ($cell) {
                    $cell->setValue('область');
                });
                $sheet->cell('O1', function ($cell) {
                    $cell->setValue('широта');
                });
                $sheet->cell('P1', function ($cell) {
                    $cell->setValue('довгота');
                });
                $sheet->cell('Q1', function ($cell) {
                    $cell->setValue('дата створення об\'єкта');
                });

            });
        })->download('csv');
    }

    public function facebook(Request $request)
    {
        $object = Object::findOrFail($request->id);

        return view('facebook', compact('object'));
    }
}
