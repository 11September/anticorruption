<?php

namespace App\Http\Controllers;

use App\City;
use App\Object;
use App\Region;
use App\Finance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadImagesDeleted;
use TCG\Voyager\Facades\Voyager;

class VoyagerObjectsController extends Controller
{

    public function getSlug(Request $request)
    {
        if (isset($this->slug)) {
            $slug = $this->slug;
        } else {
            $slug = explode('.', $request->route()->getName())[1];
        }

        return $slug;
    }

    public function validateBread($request, $data)
    {
        $rules = [];
        $messages = [];

        foreach ($data as $row) {
            $options = json_decode($row->details);

            if (isset($options->validation)) {
                if (isset($options->validation->rule)) {
                    if (!is_array($options->validation->rule)) {
                        $rules[$row->field] = explode('|', $options->validation->rule);
                    } else {
                        $rules[$row->field] = $options->validation->rule;
                    }
                }

                if (isset($options->validation->messages)) {
                    foreach ($options->validation->messages as $key => $msg) {
                        $messages[$row->field . '.' . $key] = $msg;
                    }
                }
            }
        }

        return Validator::make($request, $rules, $messages);
    }

    public function index(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        Voyager::canOrFail('browse_' . $dataType->name);

        $dataTypeContent = Object::select('id', 'name', 'address', 'created_at')->get();

        $isModelTranslatable = false;

        // Check if server side pagination is enabled
        $isServerSide = false;

        return view('voyager.objects-index', compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'isServerSide'
        ));
    }

    public function show(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        Voyager::canOrFail('read_' . $dataType->name);

        $dataTypeContent = Object::where("id", $id)->with('city', 'category', 'documents', 'customer', 'contractor', 'finances')->first();

        $isModelTranslatable = false;

        $view = 'voyager::bread.read';

        if (view()->exists("voyager::$slug.read")) {
            $view = "voyager::$slug.read";
        }

        return view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $regions = Region::all();

        $object = Object::where('id', $id)->first();
        $addressPlaceholder = $object->address;

        // Check permission
        Voyager::canOrFail('edit_' . $dataType->name);

        $dataTypeContent = Object::where('id', $id)->with('category', 'region', 'documents', 'customer', 'contractor', 'city')->first();

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        $isModelTranslatable = false;

        return view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'regions', 'addressPlaceholder'));
    }

    public function update(Request $request, $id)
    {
        $cities = City::all();
        $regions = Region::all();
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        Voyager::canOrFail('add_' . $dataType->name);

        //Validate fields with ajax
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "address" => "required",
            "city_id" => "required",
            "category_id" => "required",
            "customer_id" => "required",
            "contractor_id" => "required",
            "region_id" => "required",
            "price" => "required|integer|min:0",
            "status" => "required",
            //"description" => "required",
            "maps_lat" => "required",
            "maps_lng" => "required",
            //"finished_year" => "",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        }

        $city = City::where('id', $request->city_id)->first() ? City::where('id', $request->city_id)->first() : City::where('name', $request->city_id)->first();

        if ($city) {
            $city_id = $city->id;
        }

        if (!$city) {
            $newCity = new City();
            $newCity->name = $request->city_id;
            $newCity->save();
            $city_id = $newCity->id;
        }

        $region_id = $request->region_id;

        if ($request->region_id == 'місто Київ' || $request->region_id == 'город Киев') {
            foreach ($regions as $region) {
                if ($region->name_ua == 'Київська область') {
                    $region_id = $region->id;
                }
            }
        }

        if (!$request->ajax()) {
            dd($request);
            $object = Object::where('id', $id)->first();

            $object->name = $request->name;
            $object->address = $request->address;
            $object->city_id = $city_id;
            $object->category_id = $request->category_id;
            $object->customer_id = $request->customer_id;
            $object->contractor_id = $request->contractor_id;
            $object->region_id = $region_id;
            $object->price = $request->price;
            $object->status = $request->status;
            $object->description = $request->description;
            $object->work_description = $request->work_description;
            $object->maps_lat = $request->maps_lat;
            $object->maps_lng = $request->maps_lng;
            $object->finished_year = date('Y', strtotime($request->finished_at));
            $object->finished_at = date('Y-m', strtotime($request->finished_at));

            $object->save();

            $finances = Finance::where('object_id', $id)->first();
            $finances->suma = $request->price;
            $finances->status = 'provided';
            $finances->description = '';
            $finances->date = Carbon::now()->toDateString();
            $finances->object_id = $object->id;
            $finances->save();


            return redirect()
                ->route("voyager.{$dataType->slug}.edit", ['id' => $object->id])
                ->with([
                    'message' => "Successfully Updated {$dataType->display_name_singular}",
                    'alert-type' => 'success',
                ]);
        }
    }

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        Voyager::canOrFail('add_' . $dataType->name);

        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? new $dataType->model_name()
            : false;

        $regions = Region::all();

        $isModelTranslatable = false;

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'regions'));
    }

    public function store(Request $request)
    {
        $cities = City::all();
        $regions = Region::all();
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        Voyager::canOrFail('add_' . $dataType->name);

        //Validate fields with ajax
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "address" => "required",
            "city_id" => "required",
            "category_id" => "required",
            "customer_id" => "required",
            "contractor_id" => "required",
            "region_id" => "required",
            "price" => "required|integer|min:0",
            "status" => "required",
            //"description" => "required",
            "maps_lat" => "required",
            "maps_lng" => "required",
            //"finished_year" => "",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        }

        foreach ($cities as $city) {
            if ($request->city_id == $city->name) {
                $city_id = $city->id;
            }
        }
        if (!isset($city_id)) {
            $newCity = new City();
            $newCity->name = $request->city_id;
            $newCity->save();
            $city_id = $newCity->id;
        }

        $region_id = $request->region_id;

        if ($request->region_id == 'місто Київ' || $request->region_id == 'город Киев') {
            foreach ($regions as $region) {
                if ($region->name_ua == 'Київська область') {
                    $region_id = $region->id;
                }
            }
        }

        if (!$request->ajax()) {

            $object = new Object();

            $object->name = $request->name;
            $object->address = $request->address;
            $object->city_id = $city_id;
            $object->category_id = $request->category_id;
            $object->customer_id = $request->customer_id;
            $object->contractor_id = $request->contractor_id;
            $object->region_id = $region_id;
            $object->price = $request->price;
            $object->status = $request->status;
            $object->description = $request->description;
            $object->work_description = $request->work_description;
            $object->maps_lat = $request->maps_lat;
            $object->maps_lng = $request->maps_lng;
            $object->finished_year = date('Y', strtotime($request->finished_at));
            $object->finished_at = date('Y-m', strtotime($request->finished_at));

            $object->save();

            $finances = new Finance();
            $finances->suma = $request->price;
            $finances->status = 'provided';
            $finances->description = '';
            $finances->date = Carbon::now()->toDateString();
            $finances->object_id = $object->id;
            $finances->save();
            dd($finances);
            return redirect()
                ->route("voyager.{$dataType->slug}.edit", ['id' => $object->id])
                ->with([
                    'message' => "Successfully Added New {$dataType->display_name_singular}",
                    'alert-type' => 'success',
                ]);
        }
    }


    public function destroy(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('delete', app($dataType->model_name));

        // Init array of IDs
        $ids = [];
        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL or Model Binding
            $ids[] = $id instanceof Model ? $id->{$id->getKeyName()} : $id;
        }
        foreach ($ids as $id) {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
            $this->cleanup($dataType, $data);
        }

        $displayName = count($ids) > 1 ? $dataType->display_name_plural : $dataType->display_name_singular;

        $res = $data->destroy($ids);
        $data = $res
            ? [
                'message' => __('voyager.generic.successfully_deleted') . " {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message' => __('voyager.generic.error_deleting') . " {$displayName}",
                'alert-type' => 'error',
            ];

        if ($res) {
            event(new BreadDataDeleted($dataType, $data));
        }

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }


    protected function cleanup($dataType, $data)
    {
        // Delete Translations, if present
        if (is_bread_translatable($data)) {
            $data->deleteAttributeTranslations($data->getTranslatableAttributes());
        }

        // Delete Images
        $this->deleteBreadImages($data, $dataType->deleteRows->where('type', 'image'));

        // Delete Files
        foreach ($dataType->deleteRows->where('type', 'file') as $row) {
            $files = json_decode($data->{$row->field});
            if ($files) {
                foreach ($files as $file) {
                    $this->deleteFileIfExists($file->download_link);
                }
            }
        }
    }

    /**
     * Delete all images related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $data
     * @param \Illuminate\Database\Eloquent\Model $rows
     *
     * @return void
     */
    public function deleteBreadImages($data, $rows)
    {
        foreach ($rows as $row) {
            $this->deleteFileIfExists($data->{$row->field});

            $options = json_decode($row->details);

            if (isset($options->thumbnails)) {
                foreach ($options->thumbnails as $thumbnail) {
                    $ext = explode('.', $data->{$row->field});
                    $extension = '.' . $ext[count($ext) - 1];

                    $path = str_replace($extension, '', $data->{$row->field});

                    $thumb_name = $thumbnail->name;

                    $this->deleteFileIfExists($path . '-' . $thumb_name . $extension);
                }
            }
        }

        if ($rows->count() > 0) {
            event(new BreadImagesDeleted($data, $rows));
        }
    }

}
