@extends('layouts.master')

@section('title', $title)

@section('content')

    <div class="row text-center">
        <div class="col-md">
            <h1>Pagar factura con PSE</h1>


            <div class="form-group">
                <a class="nav-link" href="{{url('/prueba')}}">


                <img src="{{ url('/assets/img/pse.png') }}"><br>
                <h2>Pagar</h2></a>
            </div>

        </div>

    </div>
@stop