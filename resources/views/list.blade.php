@extends('layouts.core.frontend', [
	'menu' => 'google',
])

@section('title', trans('googlesheet::messages.google_sheets'))

@section('page_header')

  <div class="page-title">
    <ul class="breadcrumb breadcrumb-caret position-right">
      <li class="breadcrumb-item"><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
    </ul>
    <h1>
      <span class="text-semibold"><span class="material-symbols-rounded">format_list_bulleted</span> {{ trans('googlesheet::messages.google_sheets') }}</span>
    </h1>
  </div>

@endsection

@section('content')
  <div class="listing-form" id="ListsIndexContainer">
    <div class="d-flex top-list-controls top-sticky-content">
      <div class="me-auto">
        @if (Auth::user()->customer->listsCount() >= 0)
          <div class="filter-box">
            <div class="checkbox inline check_all_list">
              <label>
                <input type="checkbox" name="page_checked" class="styled check_all">
              </label>
            </div>
            <div class="dropdown list_actions" style="display: none">
              <button type="button"
                      class="btn btn-secondary dropdown-toggle"
                      data-bs-toggle="dropdown"
              >
                {{ trans('messages.actions') }} <span class="number"></span><span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li>
                  <a
                          class="dropdown-item"
                          link-confirm-url="{{ action('\Wikko\Googlesheet\Controllers\GoogleController@deleteConfirm') }}"
                          link-method="POST"
                          href="{{ action('\Wikko\Googlesheet\Controllers\GoogleController@delete') }}"
                  >
                    <span class="material-symbols-rounded">delete_outline</span> {{ trans('messages.delete') }}</a>
                </li>
              </ul>
            </div>
            <span class="filter-group">
                            <span class="title text-semibold text-muted">{{ trans('messages.sort_by') }}</span>
                            <select class="select" name="sort_order">
                                <option value="created_at">{{ trans('messages.created_at') }}</option>
                                <option value="list_name">{{ trans('messages.name') }}</option>
                            </select>

                            <input type="hidden" name="sort_direction" value="desc" />
                            <button type="button" class="btn btn-light sort-direction" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" role="button" class="btn btn-xs">
                                <span class="material-symbols-rounded">sort</span>
                            </button>
                        </span>
            <span class="text-nowrap">
                            <input type="text" name="keyword" class="form-control search" value="{{ request()->keyword }}" placeholder="{{ trans('messages.type_to_search') }}" />
                            <span class="material-symbols-rounded">search</span>
                        </span>
          </div>
        @endif
      </div>
      <div class="text-end">
        <a href="{{ action("\Wikko\Googlesheet\Controllers\GoogleController@create") }}" role="button" class="btn btn-secondary">
          <span class="material-symbols-rounded">add</span> {{ trans('googlesheet::messages.add_connection') }}
        </a>
      </div>
    </div>

    <div id="ListsIndexContent"></div>
  </div>

  <script>
    var ListsIndex = {
      list: null,
      getList: function() {
        if (this.list == null) {
          this.list = makeList({
            url: '{{ action('\Wikko\Googlesheet\Controllers\GoogleController@listing') }}',
            container: $('#ListsIndexContainer'),
            content: $('#ListsIndexContent')
          });
        }
        return this.list;
      }
    };

    $(document).ready(function() {
      ListsIndex.getList().load();
    });
  </script>
@endsection
