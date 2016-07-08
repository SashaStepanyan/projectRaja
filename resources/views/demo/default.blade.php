@extends('layouts.app')

@section('content')
    <div id="welcome">
        <div class="jumbotron">
            <div class="container">
                <h1 class="text-center">Demo Data Grid</h1>

            </div>
        </div>
        @if(!empty($text))
            <div class="container">{!! $text !!}</div>
        @endif
        <div class="container">
            <?= $grid ?>
        </div>
    </div>
@stop
