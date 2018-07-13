@extends('layouts.master')

@section('title', $title)

@section('content')

    <div class="title2 m-b-md">
        <h2>Listado de transacciones</h2>
    </div>
    <table class="table">
        <thead class="thead-light">
        <tr>
            <th>ID:</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Respuesta</th>
        </tr>
        </thead>
        <tbody>


    @if(!is_null($TransactionHistory))
        @foreach($TransactionHistory as $Transaction)
            <tr>
            <div class="row">
                <div class="column">
                    <td>
                    {{ $Transaction->transactionID }}</td>
                </div>
                <div class="column">
                    <td>
                    {{ $Transaction->transactionState }}</td>
                </div>
                <div class="column">
                    <td>
                    {{ $Transaction->requestDate }}
                    </td>
                </div>
                <div class="column">
                    <td>
                    {{ $Transaction->responseReasonText }}
                    </td>
                </div>
            </div>
        @endforeach
    @else
        <div>
            <strong>Transaction History Empty!</strong>
        </div>
        <br />
            </tr>
    @endif

        </tbody>
    </table>


    <div class="form-group text-center">
        <a href="/"><button class="btn btn-secondary" style="padding:8px 100px;">
                Atrás
            </button></a>

        </button> </div>




@stop