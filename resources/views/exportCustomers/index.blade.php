@extends('layouts.master')

@section('title', 'Page Title')

@section('sidebar')
    <!-- @parent -->
    <!-- p>This is appended to the master sidebar.</p -->
@stop

@section('content')
<!--
<div class="container">

</div>
-->
<div class="container" ng-app="todoApp" ng-controller="todoController" >
  <h1>EXPORTADOR DE CONTACTOS</h1>

  

  @if (isset($error))
  <div class="alert alert-danger">
    <strong>Error</strong> {{$error}}
  </div>
  @else
  

    <div><h3> Agregar nueva cuenta de Envialo Simple</h3>  </div>


    
  <form action="/tiendaNuebePlugin/public/main" method="POST" class="form-inline">
    <div class="form-group mb-2">
     
      <input type="text" readonly class="form-control-plaintext" value="API Key">
    </div>
    <div class="form-group mx-sm-3 mb-2">
    
      <input type="text" class="form-control" placeholder="Api key" name="apikey" required>
    </div>
    <input type="hidden" value="{{$code}}" name="code" />
    <input type="hidden" value="{{$tiendaId}}" name="tiendaId" />
    
    <input type="hidden" value="1" name="new" />

    <input type="submit" class="btn btn-primary mb-2" value="Agregar Cuenta">
  </form>
  @endif
    
<!--
  <p>Lista de cuentas de Envialo Simple ya vinculadas:</p>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ApiKey</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($envialosApiKeys as $apiKey)
      <tr>
        <td>{{$apiKey['apikey']}}</td>
        <td> 
          <button type="button" class="btn btn-danger btn-sm">Borrar</button>
          <button type="button" class="btn btn-primary btn-sm">Exportar</button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  -->
  
	
</div>

@stop
