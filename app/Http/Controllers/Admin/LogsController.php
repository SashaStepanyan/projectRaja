<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Logs;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Grids;
use Html;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
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
use Nayjest\Grids\Components\TotalsRow;
use Nayjest\Grids\DbalDataProvider;
use Nayjest\Grids\EloquentDataProvider;
use Nayjest\Grids\FieldConfig;
use Nayjest\Grids\FilterConfig;
use Nayjest\Grids\Grid;
use Nayjest\Grids\GridConfig;
use DB;
use Illuminate\Support\Facades\Session;

class LogsController extends Controller
{
    public function index(){

        if(Session::has('entid')){
            Session::forget('entid');
        }
        if(Session::has('perid')){
            Session::forget('perid');
        }
        Cache::flush();
        $grid = new Grid(
            (new GridConfig)
                ->setDataProvider(
                    new EloquentDataProvider(
                        Logs
                            ::leftJoin('users', 'logs.user_id', '=', 'users.id')
                            ->selectRaw('logs.*,CONCAT("{\"old\":",old_value, ",", "\"new\":",new_value,"}") as oldAndNew')
                            ->addSelect('users.name as user_name')



                    )
                )
                ->setName('persons')
                ->setPageSize(15)
                ->setColumns([
//                    (new FieldConfig)
//                        ->setName('id')
//                        ->setLabel('Check')
//                        ->setCallback(function ($id) {
//                            $checkid = (Session::has("perid") && in_array($id,Session::get("perid"))) ? "checked":"";
//                            return
//                                '<input class="ids" data-name="per" type="checkbox" value="' . $id . '" '.$checkid.'/>';
//
//                        })
//                    ,
                    (new FieldConfig)
                        ->setName('table')
                        ->setLabel('Table Name')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('action')
                        ->setLabel('Action')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('user_name')
                        ->setLabel('User')
                        ->setSortable(true)
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('oldAndNew')
                        ->setLabel('What is changed')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                        ->setCallback(function ($oldAndNew) {
                            $oldAndNewObj=json_decode($oldAndNew);
                            $old_value=$oldAndNewObj->old;
                            $new_value=$oldAndNewObj->new;





                            $table='<table class="table table-responsive insidegridTable"><tr><th></th><th>Old Value</th><th>New Value</th></tr>';
                            if($old_value==null){
                                foreach ($new_value as $key=>$value){
                                    if($key=='country_id'){
                                        $key='Country';
                                        $value= DB::table('country')->where('id', $value)->value('name');
                                    }
                                    $table.='<td>'.$key.'</td><td>'.'--'.'</td><td>'.$value.'</td></tr>';

                                }
                            }else{
                                foreach ($old_value as $key=>$value){
                                    if($key=='country_id'){
                                        $oldCountry= DB::table('country')->where('id', $value)->value('name');
                                        $newCountry= DB::table('country')->where('id', $new_value->$key)->value('name');
                                        $table.='<td>'.'Country'.'</td><td>'.$oldCountry.'</td><td>'.$newCountry.'</td></tr>';

                                    }else
                                        $table.='<td>'.$key.'</td><td>'.$value.'</td><td>'.$new_value->$key.'</td></tr>';
                                }
                            }





                            $table.='</table>';


                            return $table;

                        })
                    ,


                    (new FieldConfig)
                        ->setName('when')
                        ->setLabel('When')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )





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
//                            (new FiltersRow)
//                                ->addComponents([
//                                    (new RenderFunc(function () {
//                                        return Html::style('js/daterangepicker/daterangepicker-bs3.css')
//                                        . Html::script('js/moment/moment-with-locales.js')
//                                        . Html::script('js/daterangepicker/daterangepicker.js')
//                                        . "<style>
//                                                .daterangepicker td.available.active,
//                                                .daterangepicker li.active,
//                                                .daterangepicker li:hover {
//                                                    color:black !important;
//                                                    font-weight: bold;
//                                                }
//                                           </style>";
//                                    }))
//                                    ,
//
//                                ])
//                            ,
                            (new OneCellRow)
                                ->setRenderSection(RenderableRegistry::SECTION_END)
                                ->setComponents([
                                    (new RecordsPerPage)
                                        ->setVariants([
                                            20,
                                            30,
                                            50
                                        ]),
                                    (new ColumnsHider),
//                                    (new CsvExport)
//                                        ->setFileName('my_csv_report' . date('Y-m-d'))
//                                    ,
//                                    (new HtmlTag)
//                                        ->setContent('<span class="glyphicon glyphicon-export"></span> Excel Export')
//                                        ->setTagName('a')
//                                        ->setRenderSection(RenderableRegistry::SECTION_END)
//                                        ->setAttributes([
//                                            'id' => 'xlsexport',
//                                            'style' => 'margin-right: 4px',
//                                            'class' => 'btn btn-sm btn-success',
//                                            'href' => url('/admin/person/xls-export', ['params' => $uriParams])
//                                        ]),
//                                    (new HtmlTag)
//                                        ->setContent('<span class="glyphicon glyphicon-export"></span> PDF Export')
//                                        ->setTagName('a')
//                                        ->setRenderSection(RenderableRegistry::SECTION_END)
//                                        ->setAttributes([
////                                            'type'=>'button',
//                                            'id' => 'pdfexport',
//                                            'style' => 'margin-right: 4px',
//                                            'class' => 'btn btn-sm btn-success',
//                                            'href' => url('/admin/person/pdf-export', ['params' => $uriParams])
//                                        ]),
                                    (new HtmlTag)
                                        ->setContent('<span class="glyphicon glyphicon-refresh"></span>Filter')
                                        ->setTagName('button')
                                        ->setRenderSection(RenderableRegistry::SECTION_END)
                                        ->setAttributes([
                                            'class' => 'btn btn-success btn-sm'
                                        ]),
//                                    (new HtmlTag)
//                                        ->setContent('Reset')
//                                        ->setTagName('button')
//                                        ->setRenderSection(RenderableRegistry::SECTION_END)
//                                        ->setAttributes([
//                                            'class' => 'btn btn-sm btn-success',
////                                            'type' => 'button',
//                                            'id' => 'reset'
//                                        ])
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
        return view('logs.index',compact('text','grid'));
    }
}
