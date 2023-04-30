@extends('layouts.core.backend', [
    'menu' => 'setting',
])

@section('title', trans('messages.settings'))

@section('head')
	<script type="text/javascript" src="{{ URL::asset('core/tinymce/tinymce.min.js') }}"></script>        
    <script type="text/javascript" src="{{ URL::asset('core/js/editor.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li class="breadcrumb-item"><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
        </ul>
        <h1>
            <span class="text-gear"><span class="material-symbols-rounded">tune</span> {{ trans('messages.settings') }}</span>
        </h1>
    </div>

@endsection

@section('content')
    <form action="{{ action('\Wikko\Googlesheet\Controllers\GoogleSettingsController@save') }}" method="POST" class="form-validate-jqueryz" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="tabbable">

            <div class="tab-content">
                <div class="tab-pane active" id="top-general">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="text-semibold">{{ trans('googlesheet::messages.google_sheet_settings') }}</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group control-text">
                                <label>
                                    {{ trans('googlesheet::messages.google_sheet_client') }}
                                </label>
                                <input placeholder="Enter Google Sheet Client" value="{{$settings->google_sheet_client??''}}" type="text" name="google_sheet_client" class="form-control  ">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group control-text">
                                <label>
                                    {{ trans('googlesheet::messages.google_sheet_secret') }}
                                </label>
                                <input placeholder="Enter Google Sheet Secret" value="{{$settings->google_sheet_secret??''}}" type="text" name="google_sheet_secret" class="form-control  ">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12"><p align="right"><a href="{{ action('Admin\SettingController@advanced') }}">{{ trans('messages.configuration.settings') }}</a></p></div>
                    </div>
                    <div class="text-left">
                        <button class="btn btn-secondary"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <script>
        function changeSelectColor() {
            $('.select2 .select2-selection__rendered, .select2-results__option').each(function() {
                var text = $(this).html();
                if (text == '{{ trans('messages.default') }}') {
                    if($(this).find("i").length == 0) {
                        $(this).prepend("<i class='icon-square text-teal-600'></i>");
                    }
                }
                if (text == '{{ trans('messages.blue') }}') {
                    if($(this).find("i").length == 0) {
                        $(this).prepend("<i class='icon-square text-blue'></i>");
                    }
                }
                if (text == '{{ trans('messages.green') }}') {
                    if($(this).find("i").length == 0) {
                        $(this).prepend("<i class='icon-square text-green'></i>");
                    }
                }
                if (text == '{{ trans('messages.brown') }}') {
                    if($(this).find("i").length == 0) {
                        $(this).prepend("<i class='icon-square text-brown'></i>");
                    }
                }
                if (text == '{{ trans('messages.pink') }}') {
                    if($(this).find("i").length == 0) {
                        $(this).prepend("<i class='icon-square text-pink'></i>");
                    }
                }
                if (text == '{{ trans('messages.grey') }}') {
                    if($(this).find("i").length == 0) {
                        $(this).prepend("<i class='icon-square text-grey'></i>");
                    }
                }
                if (text == '{{ trans('messages.white') }}') {
                    if($(this).find("i").length == 0) {
                        $(this).prepend("<i class='icon-square text-white'></i>");
                    }
                }
            });
        }

        $(document).ready(function() {
            setInterval("changeSelectColor()", 100);
        });
    </script>
@endsection
