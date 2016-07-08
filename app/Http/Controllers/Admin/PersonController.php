<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Logs;
use App\Person;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonRequest;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


use Html;
use Form;
use Nayjest\Grids\Components\Base\RenderableRegistry;
use Nayjest\Grids\Components\ColumnHeadersRow;
use Nayjest\Grids\Components\ColumnsHider;
use Nayjest\Grids\Components\CsvExport;
use Nayjest\Grids\Components\FiltersRow;
use Nayjest\Grids\Components\HtmlTag;
use Nayjest\Grids\Components\Laravel5\Pager;
use Nayjest\Grids\Components\OneCellRow;
use Nayjest\Grids\Components\RecordsPerPage;
use Nayjest\Grids\Components\RenderFunc;
use Nayjest\Grids\Components\ShowingRecords;
use Nayjest\Grids\Components\TFoot;
use Nayjest\Grids\Components\THead;
use Nayjest\Grids\EloquentDataProvider;
use Nayjest\Grids\FieldConfig;
use Nayjest\Grids\FilterConfig;
use Nayjest\Grids\Grid;
use Nayjest\Grids\GridConfig;
use PDF;
use Excel;

use Response;
use Auth;

class PersonController extends Controller
{
    public static $ids;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if(Session::has('entid')){
            Session::forget('entid');
        }
        
        $input = Input::all();
        $arr = array();
        if (!empty($input['persons']['filters'])) {

            foreach ($input['persons']['filters'] as $k => $v) {
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


        $uriParams = implode('%', $arr);

        $grid = new Grid(
            (new GridConfig)
                ->setDataProvider(
                    new EloquentDataProvider(
                        Person
                        ::leftJoin('country', 'persons.country_id', '=', 'country.id')
                        ->select('persons.*')
                        // Column alias 'country_name' used to avoid naming conflicts, suggest that customers table also has 'name' column.
                        ->addSelect('country.name as country_name'))
                )
                ->setName('persons')
                ->setPageSize(15)
                ->setColumns([
                    (new FieldConfig)
                        ->setName('id')
                        ->setLabel('Check')
                        ->setCallback(function ($id) {
                            $checkid = (Session::has("perid") && in_array($id,Session::get("perid"))) ? "checked":"";
                            return
                                '<input class="ids" data-name="per" type="checkbox" value="' . $id . '" '.$checkid.'/>';

                        })
                    ,
                    (new FieldConfig)
                        ->setName('firstname')
                        ->setLabel('First Name')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('lastname')
                        ->setLabel('Last Name')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('job')
                        ->setLabel('Job/Designation')
                        ->setSortable(true)
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('email')
                        ->setLabel('Email')
                        ->setSortable(true)
                        ->setCallback(function ($val) {
                            return
                                '<small>'
                                . HTML::link("mailto:$val", $val)
                                . '</small>';
                        })
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('country_name')
                        ->setLabel('Country')
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_EQ)
                                ->setFilteringFunc(function ($val, EloquentDataProvider $provider) {
                                    $provider->getBuilder()->where('country.name', 'like', '%' . $val . '%');
                                })
                        )
                        ->setSortable(true)
                    ,
                    (new FieldConfig)
                        ->setName('city')
                        ->setLabel('City')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('tags')
                        ->setLabel('Tags')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,

                    (new FieldConfig)
                        ->setName('notes')
                        ->setLabel('Notes')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('id')
                        ->setLabel('Actions')
                        ->setCallback(function ($id) {
                            $status = Person::find($id)->status;

                            $status = ($status == 0) ? 'Enable' : 'Disable';
                            $class = ($status == 0) ? 'btn-success' : 'btn-success';
      
                            return
                                '<div class="actions"><form onload="" class="pull-left"><input type="hidden" name="_token" value="' . csrf_token() . '">'
                                . '<span class="pull-left">'
                                . Form::button($status, array(
                                    'class' => 'btn btn-sm  pchangeStatus ' . $class,
                                    'value' => "$id",
                                    'id' => "$id",
                                    'title' => 'Change Status'
                                ))
                                . '</span></form>' . '<span class="pull-left edit">'
                                . Form::button('<i class="glyphicon glyphicon-pencil"></i> ', array(
                                    'class' => 'btn btn-sm btn-success peditPerson',
                                    'value' => "$id",
                                    'title' => 'Edit Person'
                                ))
                                .'</span><span class="pull-left view">'.  Form::button('<i class="glyphicon glyphicon-eye-open"></i> ', array(
                                    'class' => 'btn btn-sm btn-success pviewPerson',
                                    'value' => "$id",
                                    'title' => 'View Person'
                                )).'</span>';

                        })
                    ,
                ])
                ->setComponents([
                    (new THead)
                        ->setComponents([
                            (new ColumnHeadersRow)->addComponent((new RenderFunc(function () {
                                return "<style>
#persons thead:first-child {border: 1px solid #ddd !important}
#persons tr > td, th {border: 1px solid #ddd !important}
#persons  {margin-top: 40px}
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
                                    ]),
                                    (new HtmlTag)
                                        ->setContent('<span class="glyphicon glyphicon-export"></span> Excel Export')
                                        ->setTagName('a')
                                        ->setRenderSection(RenderableRegistry::SECTION_END)
                                        ->setAttributes([
                                            'id' => 'xlsexport',
                                            'style' => 'margin-right: 4px',
                                            'class' => 'btn btn-sm btn-success',
                                            'href' => url('/admin/person/xls-export', ['params' => $uriParams])
                                        ]),
                                    (new HtmlTag)
                                        ->setContent('<span class="glyphicon glyphicon-export"></span> PDF Export')
                                        ->setTagName('a')
                                        ->setRenderSection(RenderableRegistry::SECTION_END)
                                        ->setAttributes([
                                            'id' => 'pdfexport',
                                            'style' => 'margin-right: 4px',
                                            'class' => 'btn btn-sm btn-success',
                                            'href' => url('/admin/person/pdf-export', ['params' => $uriParams])
                                        ]),
                                    (new HtmlTag)
                                        ->setContent('<span class="glyphicon glyphicon-refresh"></span>Filter')
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
                                            'class' => 'btn btn-sm btn-success',
                                            'type' => 'button',
                                            'id' => 'reset'
                                        ])
                                ])

                        ])
                    ,
                    (new TFoot)
                        ->setComponents([
                            (new OneCellRow)
                                ->setComponents([
                                    (new Pager)
                                        ->setRenderSection(RenderableRegistry::SECTION_END),

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

//        Session::forget('id');
        // load the view and pass the nerds
        return view('person.index', compact('grid', 'country'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $country = Country::lists('name', 'id')->all();
        return (String)view('person.create', compact('country'));


    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = Input::all();

        if (!Input::has('key_contact')) {
            $input['key_contact'] = 'no';
        }
        $messages = [
            'country_id.required' => 'The country field is required.!',
        ];
        $validator = Validator::make($input, Person::rules(), $messages);
        if ($validator->passes()) {
            $input['status']=1;

            //logs
            unset($input['_token']);
            $logs=new Logs();
            $logs->table="persons";
            $logs->user_id=Auth::id();
            $logs->action="create";
            $logs->old_value="null";
            $logs->new_value=json_encode($input);
            $logs->when=date("Y-m-d H:i:s");
            $logs->save();
            Person::create($input);
            return response()->json(['status' => 'success', 'errors' => 'no']);
        } else {
            return response()->json(['status' => 'failed', 'errors' => $validator->errors()]);
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
        $person = Person::find($id);
        $country = Country::lists('name', 'id')->all();
        return (String)view('person.edit', compact('person', 'country'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function view()
    {
        $input = Input::all();
        $id=$input['id'];
        $person = Person::find($id);
        $country = Country::find($person->country_id);
        
        return (String)view('person.view', compact('person', 'country'));

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

        if (!Input::has('key_contact')) {
            $input['key_contact'] = 'no';
        }


        $validator = Validator::make($input, Person::rules($id));

        if ($validator->passes()) {
            $person = Person::find($id);
            $orginal=$person->getOriginal();
            $person->update($input);
            $newChanges=array_diff( $person->toArray(),$orginal);
            $oldChanges=array_diff( $orginal,$person->toArray());

            unset($newChanges['updated_at'],$newChanges['created_at']);
            unset($oldChanges['updated_at'],$oldChanges['created_at']);
            if(!empty($newChanges)){
                $logs=new Logs();
                $logs->table="persons";
                $logs->user_id=Auth::id();
                $logs->action="update";
                $logs->old_value=json_encode($oldChanges);
                $logs->new_value=json_encode($newChanges);
                $logs->when=date("Y-m-d H:i:s");
                $logs->save();
            }

            return response()->json(['status' => 'success', 'errors' => 'no']);
            //return redirect()->route('admin.person.index');
        } else {
            return response()->json(['status' => 'failed', 'errors' => $validator->errors()]);

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

    public function changeStatus($id)
    {
        $person = Person::find($id);
        $person->status = !$person->status;
        $person->save();
        $status = ($person->status == 1) ? 'Disable' : 'Enable';
        return response()->json(['status' => $status]);

    }

    public function generate_pdf($params = null)
{

     
    if (Session::has('perid')) {
        $ids = Session::get('perid');
        Session::forget('perid');

    } else {
        $ids = null;
    }
    $colums = json_decode($_COOKIE["persons-columns_hider-cookie"]);
    ini_set('memory_limit', '-1');

    $arrPersonTitle =[];
    foreach ($colums as $colum => $val){
        if ($val == true  && $colum != 'id' && $colum != 'all' && $colum != 'action'){
            $arrPersonTitle[] =$colum;
        }
    };


    $data = Person::getPersons($params, $colums, $ids);

    $pdf = PDF::loadView('pdf.document', ['persons' => $data, 'PersonTitles' => $arrPersonTitle]);
    return $pdf->download('Persons List.pdf');
}
    

    public function generate_xls($params = null)
    {

        if (Session::has('perid')) {
            $ids = Session::get('perid');
            Session::forget('perid');
        } else {
            $ids = null;
        }
        $colums = json_decode($_COOKIE["persons-columns_hider-cookie"]);
        ini_set('memory_limit', '-1');

        $data = Person::getPersons($params, $colums, $ids);
        Excel::create('Persons List', function ($excel) use ($data) {

            $excel->sheet('list', function ($sheet) use ($data) {
                $sheet->fromArray($data);

            });

        })->export('xls');
    }

    function setids()
    {
        $input = Input::all();
        $data = $input['id'];
        $sessKey = $data['n'].'id';

        if (Input::has('id')) {
            if (Session::has($sessKey)) {
                // All checked
                if(isset($input['all']) && $input['all'] == 'true'){
                    $id = Session::get($sessKey);
                    $result1 = array_diff($data['id'], $id);

                    if (!empty($result1)) {
                        foreach ($result1 as $v) {
                            $id[] = $v;
                        }
                        Session::put($sessKey, $id);
                    }

                } //All unchecked
                elseif (isset($input['all']) && $input['all'] == 'false') {
                    $id = Session::get($sessKey);
                    foreach ($data['id'] as $k => $v) {
                        $key = array_search($v, $id);
                        unset($id[$key]);
                    }

                    if (empty($id)) {
                        Session::forget($sessKey);
                    } else {
                        Session::put($sessKey, $id);
                    }
                } // Check one OR uncheck one
                elseif (!is_array($data['id'])) {
                    if($data['ch'] == 'true'){
                        $id = Session::get($sessKey);
                        $id[] = $data['id'];
                        Session::put($sessKey, $id);
                    } else {
                        $id = Session::get($sessKey);
                        $key = array_search($data['id'], $id);
                        unset($id[$key]);
                        if (empty($id)) {
                            Session::forget($sessKey);
                        } else {
                            Session::put($sessKey, $id);
                        }
                    }
                }
            } else {
                if (is_array($data['id'])) {
                    Session::put($sessKey, $data['id']);
                } else {
                    Session::put($sessKey, array($data['id']));
                }

            }
        }
    }

    public function forget()
    {
        $input = Input::all();

        if (Input::has('session')) {
            $data = $input['session'];
            if($data == 'ent'){
                Session::forget('entid');
            }elseif ($data = 'per'){
                Session::forget('perid');
            }
        }
    }


}
