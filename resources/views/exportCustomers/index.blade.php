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
  
  <p>Lista de cuentas de Envialo Simple ya vinculadas:</p>
  <p>
    <form action="/tiendaNuebePlugin/public/agregarcuenta" method="POST">
      <input type="hidden" value="{{$tokenType}}" name="tokenType" />
      <input type="hidden" value="{{$tiendaToken}}" name="tiendaToken" />
      <input type="hidden" value="{{$tiendaId}}" name="tiendaId" />
      
      <input type="submit" class="btn btn-primary btn-sm" value="Agregar Cuenta">
    </form>   
   </p>
  
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
        <div class="row">
          <div class="col">
          <form action="/tiendaNuebePlugin/public/export" method="POST">
            <input type="hidden" value="{{$apiKey['apikey']}}" name="apikey" />
            <input type="hidden" value="{{$apiKey['id']}}" name="accountId" />
            <input type="hidden" value="true" name="delete" />

            <input type="hidden" value="{{$tokenType}}" name="tokenType" />
            <input type="hidden" value="{{$tiendaToken}}" name="tiendaToken" />
            <input type="hidden" value="{{$tiendaId}}" name="tiendaId" />

            <input type="submit" class="btn btn-danger btn-sm" value="Borrar">
          </form>
          </div>
          <div class="col">
          <form action="/tiendaNuebePlugin/public/main" method="POST">
            <input type="hidden" value="{{$apiKey['apikey']}}" name="apikey" />
            <input type="hidden" value="{{$apiKey['id']}}" name="tiendaId" />

            <input type="hidden" value="{{$tokenType}}" name="tokenType" />
            <input type="hidden" value="{{$tiendaToken}}" name="tiendaToken" />
            <input type="hidden" value="{{$tiendaId}}" name="tiendaId" />

            <input type="submit" class="btn btn-primary btn-sm" value="Exportar">
          </form>
          </div>
        </div>
        </td>
      </tr>
      @endforeach









    </tbody>
  </table>
  

   
  @endif
    

  
	
</div>

@stop
