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
      <a class="nav-link active" href="/pluginTN/public/export">Listas de Contactos</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">WebHooks</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/pluginTN/public/agregarcuenta">Configuracion</a>
    </li>
</ul>


  <br>
  <div><h3> Lista de la cuenta ya configuada: </h3>  </div>
  
  
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Contactos</th>
        <th>Exportar</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($lists['item'] as $item)
      <tr>
        <td>{{$item['MailListID']}}</td>
        <td>{{$item['Name']}}</td>
        <td>{{$item['ActiveMemberCount']}}</td>
        <td> 
        <form action="/pluginTN/public/pushContacts" method="POST">
            <input type="hidden" value="{{$item['MailListID']}}" name="mailListID" />
            <input type="hidden" value="{{$envialoApiKey}}" name="apikey" />
            <input type="submit" class="btn btn-primary btn-sm" value="Importar contactos de la tienda">
          </form>
        </td>
      </tr>
      @endforeach

    </tbody>
  </table>   
  @endif
</div>

@stop
