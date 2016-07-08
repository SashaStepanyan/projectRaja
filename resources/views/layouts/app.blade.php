<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title> Project</title>

    <!-- Fonts -->
    {!!Html::style('https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css')!!}
    {!!Html::style('css/fonts.googleapis.lato.css')!!}

    <!-- Styles -->
    {!!Html::style('css/theme/bootstrap.min.css')!!}
    {!!Html::style('css/theme/usebootstrap.css')!!}

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    {!! Html::script('js/bootstrap/html5shiv.js') !!}
    {!! Html::script('js/bootstrap/respond.min.js') !!}

    <![endif]-->
    <!-- JavaScripts -->
    {!! Html::script('js/jquery.min.js') !!}
    {!! Html::script('js/bootstrap/bootstrap.min.js') !!}
    {!! Html::script('js/reset.js') !!}
    {!! Html::script('js/custom.js') !!}
    {!! Html::script('js/entity.js') !!}
    {!!Html::style('css/bootstrap-select.min.css')!!}
    {!!Html::style('css/custom.css')!!}
    {!! Html::script('js/bootstrap-select.min.js') !!}
    {!! Html::script('js/action.js') !!}


</head>
<body id="app-layout">
<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a href="{{ url('/') }}" class="navbar-brand">Home</a>
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">


            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">Login</a></li>
                    <li><a href="{{ url('/register') }}">Register</a></li>
                @else
                    <li>
                        <a  href="{{ url('/admin/person') }}">Person</a>
                    </li>
                    <li>
                        <a  href="{{ url('/admin/entity') }}">Entity</a>
                    </li>
                    <li>
                        <a  href="{{ url('/admin/logs') }}">Logs</a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                        </ul>
                    </li>

                @endif
            </ul>

        </div>
    </div>
</div>
<input type="hidden" name="_token" value="{{ csrf_token() }}">
    @yield('content')

</body>
</html>
