@extends('layouts.master')

@section('title', $title)

@section('content')

    <div class="row" style="margin-top:40px">
        <div class="offset-md-1 col-md-9">
            <div class="card">
                <div class="card-header text-center">
                    Datos del pagador:, DEPURACION :(
                </div>
                @foreach($people as $dato)
                    <p> {{ $dato }} </p><br>
                @endforeach




                </div>
            </div>
        </div>
    </div>




@stop