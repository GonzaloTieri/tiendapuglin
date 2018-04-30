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
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link " href="/pluginTN/public/export">Listas de Contactos</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">WebHooks</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="/pluginTN/public/agregarcuenta">Configuracion</a>
    </li>
</ul>
    <br>
    <div><h3> Agregar nueva cuenta de Envialo Simple</h3>  </div>


    
  <form action="/pluginTN/public/export" method="POST" class="form-inline">
    <div class="form-group mb-2">
     
      <input type="text" readonly class="form-control-plaintext" value="API Key">
    </div>
    <div class="form-group mx-sm-3 mb-2">
    
      <input type="text" class="form-control" placeholder="Api key" name="apikey" required>
    </div>

    <input type="submit" class="btn btn-primary mb-2" value="Agregar Cuenta">
  </form>
  @endif
	
</div>

@stop
