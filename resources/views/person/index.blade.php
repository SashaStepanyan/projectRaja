@extends('layouts.app')

@section('content')
    <h1 class=" text-center">All Persons</h1>


    @if(!empty($text))
        <div class="container">{!! $text !!}</div>
    @endif
    <div class="container">

        <div class="row">
            <div class="col-lg-12 btn_add">
                <button class="btn btn-sm btn-success btn-large  pull-right " role="button"  data-toggle="modal" id="addNewPerson">Add New Person</button>
            </div>
            <?= $grid ?>
        </div>
    </div>


    <div class="page-loader">
        <div align="center" class="cssload-fond">
            <div class="cssload-container">
                <div class="cssload-whirlpool"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createPersonModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <button type="button" class="close"
                            data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel"> </h4>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                </div>

                <!-- Modal Footer -->

            </div>
        </div>
    </div>



@stop
