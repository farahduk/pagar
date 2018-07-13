@extends('layouts.master')

@section('title', $title)

@section('content')

    <div class="row" style="margin-top:40px">
        <div class="offset-md-1 col-md-9">
            <div class="card">
                <div class="card-header text-center">
                    Datos del pagador:
                </div>
                <div class="card-body" style="padding:30px">

                    <form action="{{ url('procesa') }}" method="POST">


                        {{ csrf_field() }}

                        <div class="form-group">

                            <label for="accountCode">Tipo de cuenta:</label>
                            <select name="accountCode" id="accountCode" class="custom-select custom-select-sm">
                                @foreach($accounts as $account)
                                    <option value="{{ $account['accountCode'] }}">{{ $account['accountType'] }}</option>
                                @endforeach
                            </select>



                        </div>



                        <div class="form-group">

                            <label for="bankCode">Seleccione su banco:</label>
                            <select name="bankCode" id="bankCode" class="custom-select custom-select-sm">
                                @foreach($bancas as $bank)
                                    <option {{ $bank->bankCode == '1022'? 'selected': '' }} value="{{ $bank->bankCode }}">{{ $bank->bankName }}</option>
                                @endforeach
                            </select>
                        </div>

                         <h2>Datos personales</h2>
                        <div class="form-group">


                            <label for="documentType">Identificación</label>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Tipo ID:</span>
                                </div>

                                <select name="documentType" class="custom-select custom-select-md">
                                    @foreach($documentsType as $documentType)
                                        <option value="{{ $documentType['documentCode'] }}">{{ $documentType['documentType'] }}</option>
                                    @endforeach
                                </select><div class="input-group-prepend">
                                    <span class="input-group-text">Num. ID:</span>
                                </div>
                                <input  name="document" id="document" type="number" class="form-control" placeholder="111111"  value="12345678" required>
                            </div>


                        </div>

                        <div class="form-group">




                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Nombre:</span>
                                </div>

                                <input type="text" name="firstName" id="firstName" class="form-control" required value="George">


                                <div class="input-group-prepend">
                                    <span class="input-group-text">Apellido:</span>
                                </div>
                                <input  name="lastName" id="lastName" type="text" class="form-control" required value="Kaplan">
                            </div>


                        </div>

                        <div class="form-group">

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Empresa:</span>
                                </div>

                                <input type="text" name="company" id="company" class="form-control" value="Los pollos hermanos">


                                <div class="input-group-prepend">
                                    <span class="input-group-text">Email:</span>
                                </div>
                                <input  name="emailAddress" id="emailAddress" type="email" class="form-control" required value="fring@correo.com">
                            </div>

                        </div>

                        <div class="form-group">

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Dirección:</span>
                                </div>

                                <input type="text" name="address" id="address" class="form-control" required value="calle 123">


                                <div class="input-group-prepend">
                                    <span class="input-group-text">Ciudad:</span>
                                </div>
                                <input  name="city" id="city" type="text" class="form-control" required value="Medellín">
                            </div>

                        </div>

                        <div class="form-group">

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pais:</span>
                                </div>

                                <input type="text" name="country" id="country" class="form-control" required value="CO">


                                <div class="input-group-prepend">
                                    <span class="input-group-text">Departamento:</span>
                                </div>
                                <input  name="province" id="province" type="text" class="form-control" required value="antioquia">
                            </div>

                        </div>
                        <div class="form-group">

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Teléfono:</span>
                                </div>

                                <input type="text" name="phone" id="phone" class="form-control" value="4444141" >


                                <div class="input-group-prepend">
                                    <span class="input-group-text">Móvil:</span>
                                </div>
                                <input  name="mobile" id="mobile" type="text" class="form-control" required value="31012345">
                            </div>

                        </div>



                        <div class="form-group text-center">
                            <a href="/"><button class="btn btn-secondary" style="padding:8px 100px;margin-top:25px;">
                                Atrás
                            </button></a>
                            <button type="submit" class="btn btn-primary" style="padding:8px 100px;margin-top:25px;">
                                Pagar
                            </button>


                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>




@stop