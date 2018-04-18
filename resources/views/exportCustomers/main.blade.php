@extends('layouts.master')

@section('title', 'Page Title')

@section('sidebar')
    <!-- @parent -->
    <!-- p>This is appended to the master sidebar.</p -->
@stop

@section('content')
<div class="container" ng-app="todoApp" ng-controller="todoController" >
	<div>
	<h1>EXPORTADOR DE CONTACTOS</h1>
	</div>

	<div>
	@if (isset($error))
	<div class="alert alert-danger">
		<strong>Error</strong> {{$error}}
	</div>
  	@else

    	<div> <h3> Seleccione un lista para exportar los contactos </h3>  </div>
		<form action="/tiendaNuebePlugin/public/pushContacts" method="POST" class="form-inline">
			<div class="form-group mb-2">
				<input type="text" readonly class="form-control-plaintext" value="Lista a exportar: ">
			</div>
			<div class="form-group mx-sm-3 mb-2">
				<select class="form-control" name="mailListID">
				@foreach ($lists['item'] as $list)
					<option value="{{ $list['MailListID'] }}">{{ $list['Name'] }}</option>
				@endforeach
				</select>
			</div>
			
			<input type="hidden" value="{{$apikey}}" name="apikey" />
            <input type="hidden" value="{{$tokenType}}" name="tokenType" />
            <input type="hidden" value="{{$tiendaToken}}" name="tiendaToken" />
            <input type="hidden" value="{{$tiendaId}}" name="tiendaId" />


			<input type="submit" class="btn btn-primary mb-2" value="Continuar">
		</form>
	@endif
	</div>
</div>

@stop