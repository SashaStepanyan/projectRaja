<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\Entity;
use App\Person;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Country;
use App\Applicant;
use App\CreditDays;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Entitiy_Address;
use App\EntityApplicant;

use Grids;
use Html;
use Form;
use Nayjest\Grids\Components\Base\RenderableRegistry;
use Nayjest\Grids\Components\ColumnHeadersRow;
use Nayjest\Grids\Components\ColumnsHider;
use Nayjest\Grids\Components\CsvExport;
use Nayjest\Grids\Components\ExcelExport;
use Nayjest\Grids\Components\Filters\DateRangePicker;
use Nayjest\Grids\Components\FiltersRow;
use Nayjest\Grids\Components\HtmlTag;
use Nayjest\Grids\Components\Laravel5\Pager;
use Nayjest\Grids\Components\OneCellRow;
use Nayjest\Grids\Components\RecordsPerPage;
use Nayjest\Grids\Components\RenderFunc;
use Nayjest\Grids\Components\ShowingRecords;
use Nayjest\Grids\Components\TFoot;
use Nayjest\Grids\Components\THead;
use Illuminate\Support\Facades\Session;
use Nayjest\Grids\Components\TotalsRow;
use Nayjest\Grids\DbalDataProvider;
use Nayjest\Grids\EloquentDataProvider;
use Nayjest\Grids\FieldConfig;
use Nayjest\Grids\FilterConfig;
use Nayjest\Grids\Grid;
use Nayjest\Grids\GridConfig;
use PDF;
use URL;
use DB;

use Excel;
use Response;


class EntityController extends Controller
{
    public function index()
    {
        if (Session::has('perid')) {
            Session::forget('perid');
        }

        $input = Input::all();
        $arr = array();
        if (!empty($input['entity']['filters'])) {

            foreach ($input['entity']['filters'] as $k => $v) {
                if ($k == 'id-like' && is_array($v) && in_array('all', $v)) {
                    $arr[] = $k . '=all';
                } elseif ($k == 'id-like' && is_array($v) && !in_array('all', $v)) {
                    $id = implode(',', $v);
                    $arr[] = $k . '=' . $id;
                } else {
                    $arr[] = $k . '=' . $v;
                }
            }
        }

        $uriParams = implode('*', $arr);

        $query = Entity::query()
            ->leftJoin('entity_applicant', 'entities.id', '=', 'entity_applicant.entities_id')
            ->leftJoin('applicant', 'applicant.id', '=', 'entity_applicant.applicant_id')
            ->leftJoin('currency', 'entities.currency_id', '=', 'currency.id')
            ->leftJoin('credit_days', 'entities.credit_days_id', '=', 'credit_days.id')
            ->leftJoin('entitiy_address', 'entitiy_address.entity_id', '=', 'entities.id')
            ->selectRaw('entities.*, applicant.name as appname, credit_days.day,
             CONCAT(entitiy_address.state, IF(entitiy_address.state !="",",",""),entitiy_address.city,IF(entitiy_address.city !="",",",""),entitiy_address.street) as default_address,
             group_concat(applicant.name) as applicants')
            ->where('entitiy_address.default', '1')
            ->groupBy('entity_applicant.entities_id');


        $grid = new Grid(

            (new GridConfig)
                ->setDataProvider(

                    new EloquentDataProvider(
                        $query
                    )


                )
                ->setName('entity')
                ->setPageSize(15)
                ->setColumns([
                    (new FieldConfig)
                        ->setName('id')
                        ->setLabel('Check')
                        ->setCallback(function ($id) {
                            $checkid = (Session::has("entid") && in_array($id, Session::get("entid"))) ? "checked" : "";
                            return
                                '<input class="idsent" data-name="ent" type="checkbox" value="' . $id . '" ' . $checkid . '>';

                        })
                    ,
                    (new FieldConfig)
                        ->setName('applicants')
                        ->setLabel('Applicant')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                                ->setFilteringFunc(function ($val, EloquentDataProvider $provider) {
                                    $Val = trim($val, " ");
                                    $provider->getBuilder()
                                        ->where('applicant.name', 'like', "%" . $Val . "%");

                                })
                        )
                    ,
                    (new FieldConfig)
                        ->setName('name')
                        ->setLabel('Entity Name')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                                ->setFilteringFunc(function ($val, EloquentDataProvider $provider) {
                                    $provider->getBuilder()
                                        ->where('entities.name', 'like', "%" . $val . "%");
                                })
                        )
                    ,
                    (new FieldConfig)
                        ->setName('existing_type')
                        ->setLabel('Existing Type')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('currency.name')
                        ->setLabel('Currency')
                        ->setSortable(true)
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('day')
                        ->setLabel('Credit Day')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                                ->setFilteringFunc(function ($val, EloquentDataProvider $provider) {
                                    $Val = trim($val, " ");
                                    $provider->getBuilder()
                                        ->where('credit_days.day', 'like', "%" . $Val . "%");
                                })
                        )
                    ,
                    (new FieldConfig)
                        ->setName('default_address')
                        ->setLabel('Default Address')
                        ->setSortable(true)
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                                ->setFilteringFunc(function ($val, EloquentDataProvider $provider) {
                                    $Val = explode(',', $val);
                                    $provider->getBuilder()
                                        ->where('entitiy_address.state', 'like', "%" . trim($Val[0], " ") . "%")
                                        ->where('entitiy_address.city', 'like', "%" . trim($Val[1], " ") . "%")
                                        ->where('entitiy_address.street', 'like', "%" . trim($Val[2], " ") . "%");
                                })
                        )
                    ,


                    (new FieldConfig)
                        ->setName('notes')
                        ->setLabel('Notes')
                        ->setSortable(true)
                        ->setCallback(function ($notes) {
                            return strlen($notes) > 150 ? substr($notes,0,150)."..." : $notes;
                        })
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('id')
                        ->setLabel('Actions')
                        ->setCallback(function ($id) {
                            $status = Entity::find($id)->status;

                            $status = ($status == 0) ? 'Enable' : 'Disable';
                            $class = ($status == 0) ? 'btn-success' : 'btn-success';

                            return
                                '<div class="actions"><form onload="" class="pull-left"><input type="hidden" name="_token" value="' . csrf_token() . '">'
                                . '<span class="pull-left">'
                                . Form::button($status, array(
                                    'class' => 'btn btn-sm  echangeStatus ' . $class,
                                    'value' => "$id",
                                    'id' => "$id",
                                    'title' => 'Change Status'
                                ))
                                . '</span></form>' . '<span class="pull-left edit">'
                                . Form::button('<i class="glyphicon glyphicon-pencil"></i> ', array(
                                    'class' => 'btn btn-sm btn-success editEntity',
                                    'value' => "$id",
                                    'title' => 'Edit Person'
                                ))
                                . '</span><span class="pull-left view">' . Form::button('<i class="glyphicon glyphicon-eye-open"></i> ', array(
                                    'class' => 'btn btn-sm btn-success viewEntity',
                                    'value' => "$id",
                                    'title' => 'View Person'
                                )) . '</span>';

                        })
                    ,
                ])
                ->setComponents([
                    (new THead)
                        ->setComponents([
                            (new ColumnHeadersRow)->addComponent((new RenderFunc(function () {
                                return "<style>
#entity thead:first-child {border: 1px solid #ddd !important}
#entity tr > td {border: 1px solid #ddd !important}
#entity  {margin-top: 40px}
</style>";
                            }))),
                            (new FiltersRow)
                                ->addComponents([
                                    (new RenderFunc(function () {
                                        return Html::style('js/daterangepicker/daterangepicker-bs3.css')
                                        . Html::script('js/moment/moment-with-locales.js')
                                        . Html::script('js/daterangepicker/daterangepicker.js')
                                        . "<style>
                                                .daterangepicker td.available.active,
                                                .daterangepicker li.active,
                                                .daterangepicker li:hover {
                                                    color:black !important;
                                                    font-weight: bold;
                                                }
                                           </style>";
                                    }))
                                    ,

                                ])
                            ,
                            (new OneCellRow)
                                ->setRenderSection(RenderableRegistry::SECTION_END)
                                ->setComponents([
                                    (new RecordsPerPage)
                                        ->setVariants([
                                            20,
                                            30,
                                            50
                                        ]),
                                    (new ColumnsHider)
                                        ->setHiddenByDefault([
                                            'notes'
                                        ])
                                    ,

                                    (new HtmlTag)
                                        ->setContent('<span class="glyphicon glyphicon-export"></span> Excel Export')
                                        ->setTagName('a')
                                        ->setRenderSection(RenderableRegistry::SECTION_END)
                                        ->setAttributes([
                                            'id' => 'xlsexport',
                                            'style' => 'margin-right: 4px',
                                            'class' => 'btn btn-sm btn-success',
                                            'href' => url('/admin/entity/xls-export', ['params' => $uriParams])
                                        ]),
                                    (new HtmlTag)
                                        ->setContent('<span class="glyphicon glyphicon-export"></span> PDF Export')
                                        ->setTagName('a')
                                        ->setRenderSection(RenderableRegistry::SECTION_END)
                                        ->setAttributes([
                                            'id' => 'pdfexport',
                                            'style' => 'margin-right: 4px',
                                            'class' => 'btn btn-sm btn-success',
                                            'href' => url('/admin/entity/pdf-export', ['params' => $uriParams])
                                        ]),
                                    (new HtmlTag)
                                        ->setContent('<span class="glyphicon glyphicon-refresh"></span> Filter')
                                        ->setTagName('button')
                                        ->setRenderSection(RenderableRegistry::SECTION_END)
                                        ->setAttributes([
                                            'class' => 'btn btn-success btn-sm'
                                        ]),
                                    (new HtmlTag)
                                        ->setContent('Reset')
                                        ->setTagName('button')
                                        ->setRenderSection(RenderableRegistry::SECTION_END)
                                        ->setAttributes([
                                            'type' => 'button',
                                            'class' => 'btn btn-sm btn-success',
                                            'id' => 'resetent'
                                        ])
                                ])

                        ])
                    ,
                    (new TFoot)
                        ->setComponents([
//                            (new TotalsRow(['posts_count', 'comments_count'])),
//                            (new TotalsRow(['posts_count', 'comments_count']))
//                                ->setFieldOperations([
//                                    'posts_count' => TotalsRow::OPERATION_AVG,
//                                    'comments_count' => TotalsRow::OPERATION_AVG,
//                                ])
//                            ,
                            (new OneCellRow)
                                ->setComponents([
                                    (new Pager),
                                    (new HtmlTag)
                                        ->setAttributes(['class' => 'pull-right'])
                                        ->addComponent(new ShowingRecords)
                                    ,

                                ])
                        ])
                    ,
                ])

        );

        $grid = $grid->render();
        $country = Country::lists('name', 'id')->all();


        // load the view and pass the nerds
        return view('entity.index', compact('grid', 'country'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $country = Country::lists('name', 'id')->all();
        $applicant = Applicant::lists('name', 'id')->all();
        $credit_days = CreditDays::lists('day', 'id')->all();
        $currency = Currency::lists('name', 'id')->all();

        return (String)view('entity.create', compact('country', 'applicant', 'credit_days', 'currency'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = Input::all();
        $entity = $input['entity'];
        $addresses = $input['address'];
        $applicant = $input['applicant'];

        $messages = [
            'applicant_id.required' => 'The applicant field is required!',
            'entity.name.required' => 'The entity name field is required!',


        ];
        $validator = Validator::make($input, Entity::rules(), $messages);

        $inputTMO = $addresses['tmo'];
        $lengthTMO = true;
        foreach ($inputTMO as $value) {
            if (strlen($value) > 10) {
                $lengthTMO = false;
            }
        }
        $TMONumValid = true;
        foreach (array_filter($inputTMO) as $value) {
            if (!is_numeric($value)) {
                $TMONumValid = false;

            }
        }

        $inputZip = $addresses['zip'];
        $ZipValid = true;
        foreach (array_filter($inputZip) as $value) {
            if (!is_numeric($value)) {
                $ZipValid = false;

            }
        }


        if (array_sum($inputTMO)) {
            $checkInputTMODub = count(array_keys(array_flip($inputTMO))) == count($inputTMO);

        } else {
            $checkInputTMODub = true;
        }


        $dbTMO = Entitiy_Address::pluck('tmo')->toArray();

        $checkInputAndDbTMODub = array_intersect($inputTMO, array_filter($dbTMO));
        $validateTMO = $checkInputTMODub && empty($checkInputAndDbTMODub);
        if ($validator->passes() && $validateTMO && $ZipValid) {
            //Entity save
            $entity['currency_id'] = ($entity['currency_id'] == '') ? NULL : $entity['currency_id'];
            $entity['credit_days_id'] = ($entity['credit_days_id'] == '') ? NULL : $entity['credit_days_id'];
            $entityModel = Entity::create($entity);

            $entity_id = $entityModel->id;
            //EntityApplicant save
            $saveApplicant = [];
            $data = [];

            for ($i = 0; $i < count($applicant); $i++) {
                $data['applicant_id'] = $applicant[$i];
                $data['entities_id'] = $entity_id;
                $saveApplicant[] = $data;
            }
            EntityApplicant::insert($saveApplicant);
            //EntityAddress Model
            $arrAddress = [];
            $tempArr = [];

            for ($i = 0; $i < count($addresses['street']); $i++) {
                $tempArr['street'] = $addresses['street'][$i];
                $tempArr['city'] = $addresses['city'][$i];
                $tempArr['state'] = $addresses['state'][$i];
                $tempArr['zip'] = $addresses['zip'][$i];
                $tempArr['country_id'] = ($addresses['country_id'][$i] == '') ? NULL : $addresses['country_id'][$i];
                $tempArr['tmo'] = ($addresses['tmo'][$i] == '') ? NULL : $addresses['tmo'][$i];
                $tempArr['entity_id'] = $entity_id;
                $tempArr['default'] = 0;
                $arrAddress[] = $tempArr;
            }
            $arrAddress[0]['default'] = 1;
            $entitiyAddressModel = Entitiy_Address::insert($arrAddress);


            return response()->json(['status' => 'success', 'errors' => 'no']);
        } else {

            $errors = $validator->errors();


            if (!$validateTMO) {

                $errors->messages['tmo'] = array('TMO must be unique value');
            }
            if (!$ZipValid) {
                $errors->messages['zip'] = array('Zip must be numeric value');
            }
            if (!$lengthTMO) {
                $errors->messages['tmo_length'] = array('The TMO code is longer!');
            }
            if (!$TMONumValid) {
                $errors->messages['tmo_length'] = array('The tmo code must be numeric value');
            }
            return response()->json(['status' => 'failed', 'errors' => $errors]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {

        $entity = Entity::find($id);

        $country = Country::lists('name', 'id')->all();
        $applicant = Applicant::lists('name', 'id')->all();
        $credit_days = CreditDays::lists('day', 'id')->all();
        $currency = Currency::lists('name', 'id')->all();
        $selectedApplicant = EntityApplicant::where('entities_id', $id)->lists('applicant_id')->toArray();
        $addresses = Entitiy_Address::where('entity_id', $id)->get();


        return (String)view('entity.edit', compact('entity', 'country', 'applicant', 'credit_days', 'currency', 'selectedApplicant', 'addresses'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {

        $input = Input::all();

        $entity = $input['entity'];
        $addresses = $input['address'];
        $applicant = $input['applicant'];

        $messages = [
            'applicant_id.required' => 'The applicant field is required!',
            'country_id.required' => 'The country field is required!',
            'entity.name.required' => 'The entity name field is required!',
        ];
        $validator = Validator::make($input, Entity::rules(), $messages);

        $inputTMO = $addresses['tmo'];
        $inputZip = $addresses['zip'];
        $ZipValid = true;
        foreach (array_filter($inputZip) as $value) {
            if (!is_numeric($value)) {
                $ZipValid = false;

            }
        }
        $lengthTMO = true;
        foreach ($inputTMO as $value) {
            if (strlen($value) > 10) {
                $lengthTMO = false;
            }
        }
        $TMONumValid = true;
        foreach (array_filter($inputTMO) as $value) {
            if (!is_numeric($value)) {
                $TMONumValid = false;

            }
        }


        if (array_sum($inputTMO)) {
            $checkInputTMODub = count(array_keys(array_flip($inputTMO))) == count($inputTMO);
        } else {
            $checkInputTMODub = true;
        }


        $dbTMO = Entitiy_Address::pluck('tmo')->toArray();

        $thisTMOS = DB::table('entitiy_address')->where('entity_id', $id)->pluck('tmo');

        $diff = array_diff(array_unique($dbTMO), $thisTMOS);

        $checkInputAndDbTMODub = array_intersect($inputTMO, array_filter($diff));
        $validateTMO = $checkInputTMODub && empty($checkInputAndDbTMODub);
        if ($validator->passes() && $validateTMO && $ZipValid && $lengthTMO) {
            //Entity save
            $entity['currency_id'] = ($entity['currency_id'] == '') ? NULL : $entity['currency_id'];
            $entity['credit_days_id'] = ($entity['credit_days_id'] == '') ? NULL : $entity['credit_days_id'];


            $entityModel = Entity::find($id);

            $entityModel->update($entity);
            $entity_id = $id;

            //EntityApplicant save
            $saveApplicant = [];
            $data = [];
            $EntityApplicantModel = EntityApplicant::where('entities_id', $id)->get();

            foreach ($EntityApplicantModel as $value) {
                if (in_array($value->applicant_id, $applicant)) {
                    $key = array_search($value->applicant_id, $applicant);
                    unset($applicant[$key]);
                } else {
                    DB::table('entity_applicant')->where('entities_id', $entity_id)->where('applicant_id', $value->applicant_id)->delete();
                }
            };
            if (!empty($applicant)) {
                for ($i = 0; $i < count($applicant); $i++) {
                    $data['applicant_id'] = $applicant[$i];
                    $data['entities_id'] = $entity_id;
                    $saveApplicant[] = $data;
                }
                EntityApplicant::insert($saveApplicant);
            }


            //EntityAddress Model
            $arrAddress = [];
            $tempArr = [];

            for ($i = 0; $i < count($addresses['street']); $i++) {
                $tempArr['id'] = $addresses['id'][$i];
                $tempArr['street'] = $addresses['street'][$i];
                $tempArr['city'] = $addresses['city'][$i];
                $tempArr['state'] = $addresses['state'][$i];
                $tempArr['zip'] = $addresses['zip'][$i];
                $tempArr['country_id'] = ($addresses['country_id'][$i] == '') ? NULL : $addresses['country_id'][$i];
                $tempArr['tmo'] = ($addresses['tmo'][$i] == '') ? NULL : $addresses['tmo'][$i];
                $tempArr['entity_id'] = $entity_id;
                $tempArr['default'] = 0;
                $arrAddress[] = $tempArr;
            }
            $arrAddress[0]['default'] = 1;
            foreach ($arrAddress as $key => $value) {
                if ($value['id'] != 0) {
                    $entityAddressModel = Entitiy_Address::find($value['id']);

                    $entityAddressModel->update($value);
                } else {
                    unset($value['id']);
                    $entitiyAddressModel = Entitiy_Address::insert($value);

                }
            };

            return response()->json(['status' => 'success', 'errors' => 'no']);
        } else {
            $errors = $validator->errors();


            if (!$validateTMO) {
                $errors->messages['tmo'] = array('TMO must be unique value');
            }
            if (!$ZipValid) {
                $errors->messages['zip'] = array('Zip must be numeric value');
            }
            if (!$lengthTMO) {
                $errors->messages['tmo_length'] = array('The TMO code is longer!');
            }
            if (!$TMONumValid) {
                $errors->messages['tmo_length'] = array('The tmo code must be numeric value');
            }
            return response()->json(['status' => 'failed', 'errors' => $errors]);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function validatetmo()
    {
        $input = Input::all();
        if (Input::has('tmo')) {
            if ($input['tmo'] == '') {
                return "success";
            }
            $tmo = Entitiy_Address::hasTmo($input['tmo']);
            if (empty($tmo) || $input['tmo'] == '') {
                return "success";
            } else {
                return ['err' => "The TMO code already exists"];
            }
        }
    }

    public function generate_pdf($params = null)
    {
        if (Session::has('entid')) {
            $ids = Session::get('entid');
            Session::forget('entid');
        } else {
            $ids = null;
        }

        $colums = json_decode($_COOKIE["entity-columns_hider-cookie"]);
        ini_set('memory_limit', '-1');
        $td = [];
        foreach ($colums as $colum => $val) {
            if ($val == true && $colum != 'id') {
                $td[] = $colum;
            }
        };

        $arrEntityTitle = [];
        foreach ($colums as $colum => $val) {
            if ($val == true && $colum != 'id' && $colum != 'all' && $colum != 'action') {
                $arrEntityTitle[] = $colum;
            }
        };

        $data = Entity::getEntitis($params, $colums, $ids);

        $pdf = PDF::loadView('pdf.ent_doc', ['persons' => $data, 'EentityTitles' => $arrEntityTitle]);
        return $pdf->download('Entities List.pdf');
    }


    public function generate_xls($params = null)
    {
        if (Session::has('entid')) {
            $ids = Session::get('entid');
            Session::forget('entid');
        } else {
            $ids = null;

        }
        $colums = json_decode($_COOKIE["entity-columns_hider-cookie"]);
        ini_set('memory_limit', '-1');

        $data = Entity::getEntitis($params, $colums, $ids);
        Excel::create('Entities List', function ($excel) use ($data) {

            $excel->sheet('list', function ($sheet) use ($data) {
                $sheet->fromArray($data);

            });

        })->export('xls');
    }

    public function destroy_entity($id)
    {
        $item = Entitiy_Address::findOrFail($id);
        $item->delete();
    }

    public function changeStatus($id)
    {
        $entity = Entity::find($id);
        $entity->status = !$entity->status;
        $entity->save();
        $status = ($entity->status == 1) ? 'Disable' : 'Enable';
        return response()->json(['status' => $status]);

    }

    public function view()
    {
        $input = Input::all();
        $id = $input['id'];

        $entity = DB::table('entities')
            ->select('entities.*', 'entitiy_address.*',
                //DB::raw("(CONCAT(entitiy_address.state, IF(entitiy_address.state !=\"\",\",\",\"\"),entitiy_address.city,IF(entitiy_address.city !=\"\",\",\",\"\"),entitiy_address.street)) as `default_address`"),
                DB::raw("(GROUP_CONCAT(applicant.name SEPARATOR ',')) as `applicants`"),
                DB::raw("(GROUP_CONCAT(entitiy_address.default,', ',entitiy_address.state,IF(entitiy_address.state !='',', ',''),entitiy_address.city,IF(entitiy_address.city !='',', ',''),entitiy_address.street SEPARATOR '/')) as `addresses`"),
                'credit_days.day as credit_day', 'currency.name as currency_name')
            ->leftJoin('entity_applicant', 'entities.id', '=', 'entity_applicant.entities_id')
            ->leftJoin('applicant', 'applicant.id', '=', 'entity_applicant.applicant_id')
            ->leftJoin('currency', 'entities.currency_id', '=', 'currency.id')
            ->leftJoin('credit_days', 'entities.credit_days_id', '=', 'credit_days.id')
            ->leftJoin('entitiy_address', 'entitiy_address.entity_id', '=', 'entities.id')
            ->where('entities.id', $id)->where('entitiy_address.entity_id', $id)
            ->groupBy('entity_applicant.entities_id', 'entitiy_address.entity_id')
            ->first();


        $country = Country::find($entity->country_id);

        return (String)view('entity.view', compact('entity', 'country'));

    }
}
