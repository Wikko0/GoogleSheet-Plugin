@extends('layouts.core.frontend', [
	'menu' => 'google',
])

@section('title', trans('googlesheet::messages.create_google_sheet'))

@section('page_header')
	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li class="breadcrumb-item"><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ action("\Wikko\Googlesheet\Controllers\GoogleController@index") }}">{{ trans('googlesheet::messages.google_sheets') }}</a></li>
		</ul>
		<h1>
			<span class="text-semibold"><span class="material-symbols-rounded">add</span> {{ trans('googlesheet::messages.create_google_sheet') }}</span>
		</h1>
	</div>
@endsection

@section('content')
	<form action="{{ action('\Wikko\Googlesheet\Controllers\GoogleController@store') }}" method="POST" class="form-validate-jqueryz">
		{{ csrf_field() }}
		<div class="row">
			<div class="col-md-6">
				<div class="form-group control-text">
					<label>
						Connection Name
						<span class="text-danger">*</span>
					</label>
					<input placeholder="" value="" type="text" name="connection_name" class="form-control required">
				</div>


				<div class="form-group control-text">
					<label>
						Connection Type
						<span class="text-danger">*</span>
					</label>
					<select name="connection_type" class="form-select" aria-label="Default select example" required>
						<option disabled selected>Choose</option>
						<option value="App to Sheets">App to Sheets</option>
						<option value="Sheets to App">Sheets to App</option>
						<option value="Full sync">Full sync</option>

					</select>
				</div>

				<div class="form-group control-select" data-select2-id="5">
					<label>
						Select List
						<span class="text-danger">*</span>
					</label>


					<select name="list_uid" class="form-select" aria-label="Default select example" required>
						<option disabled selected>Choose</option>
						@foreach($list as $value)
						<option value="{{$value->uid}}">{{$value->name}}</option>
						@endforeach
					</select>




				</div>
			</div>


			<div class="col-md-6">
				<div class="hiddable-cond">
					<div class="form-group control-autofill">
						<label>
							Sheet Name
							<span class="text-danger">*</span>
						</label>
						<input placeholder="" value="" type="text" name="sheet_name" class="form-control required  ">
					</div>

					<div class="form-group control-autofill">
						<label>
							Sheet ID
							<span class="text-danger">*</span>
						</label>
						<input placeholder="" value="" type="text" name="sheet_id" class="form-control required ">
					</div>
				</div>
			</div>
		</div>
		<hr>
		<div class="text-left">
			<button class="btn btn-secondary me-2"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
			<a href="{{ action('\Wikko\Googlesheet\Controllers\GoogleController@index') }}" class="btn btn-link"><i class="icon-cross2"></i> {{ trans('messages.cancel') }}</a>
		</div>
	</form>
@endsection
