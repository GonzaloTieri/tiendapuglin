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

<div class="container" ng-app="todoApp" ng-controller="todoController">
	<h1>Exportar contactos</h1>
	@if (isset($error))
	<div class="alert alert-danger">
		<strong>Error</strong> {{$error}}
	</div>
	@else
	<div class="row">
		<h2> Se exportaron correctamente {{$count}} contactos</h2>
	</div>
	@endif
	

<!-- div>
    <input type="submit" value="Volver al admin">
</div -->

</div>

@stop