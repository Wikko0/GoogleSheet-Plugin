@if ($lists->count() > 0)
    <table class="table table-box pml-table mt-2"
        current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
    >
        @foreach ($lists as $key => $list)
            <tr>
                <td width="1%">
                    <div class="text-nowrap">
                        <div class="checkbox inline me-1">
                            <label>
                                <input type="checkbox" class="node styled"
                                    name="uids[]"
                                    value="{{ $list->id }}"
                                />
                            </label>
                        </div>
                    </div>
                </td>
                <td width="35%">
                    <a class="kq_search fw-600 d-block list-title" href="{{ action('MailListController@overview', [
                        'uid' => $list->uid
                    ]) }}">
                        {{ $list->list_name }}
                    </a>
                    <span class="text-muted">{{ trans('messages.created_at') }}: {{ Auth::user()->customer->formatDateTime($list->created_at, 'datetime_full') }}</span>
                </td>
                <td class="stat-fix-size-sm">
                    <div class="d-flex">
                        <div class="single-stat-box pull-left ml-20">
                            <span class="no-margin text-primary stat-num">{{$list->connection_name}}</span>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-info" style="width: 100%">
                                </div>
                            </div>
                            <span class="text-muted small">{{ trans('googlesheet::messages.connection_name') }}</span>
                        </div>
                        <div class="single-stat-box pull-left ml-20">
                            <span class="no-margin text-primary stat-num">{{$list->sheet_name}}</span>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-info" style="width: 100%">
                                </div>
                            </div>
                            <span class="text-muted small">{{ trans('googlesheet::messages.sheet_name') }}</span>
                        </div>
                        <div class="single-stat-box pull-left ml-20">
                            <span class="no-margin text-primary stat-num">{{$list->connection_type}}</span>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-info" style="width: 100%">
                                </div>
                            </div>
                            <span class="text-muted small">{{ trans('googlesheet::messages.connection_type') }}</span>
                        </div>
                        <div class="single-stat-box pull-left ml-20">
                            <span class="no-margin text-primary stat-num">{{$list->last_sync??'Not synced'}}</span>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-info" style="width: 100%">
                                </div>
                            </div>
                            <span class="text-muted small">{{ trans('googlesheet::messages.last_sync') }}</span>
                        </div>
                    </div>
                </td>
                <td class="text-end pe-0">
                    <div class="d-flex align-items-center text-nowrap justify-content-end" role="group">
                        <a href="{{ action('\Wikko\Googlesheet\Controllers\GoogleController@synchronize', ['id' => $list->id]) }}" data-popup="tooltip"
                            title="{{ trans('googlesheet::messages.synchronize') }}" role="button" class="btn btn-secondary btn-icon me-1">
                            <span class="material-symbols-rounded">person_add</span>
                        </a>
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-light btn-icon dropdown-toggle ps-2"  data-bs-toggle="dropdown" aria-expanded="false">

                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnGroupDrop1">

                                <li><a class="dropdown-item" href="{{ action('\Wikko\Googlesheet\Controllers\GoogleController@edit', $list->id) }}">
                                    <span class="material-symbols-rounded me-2">edit</span> {{ trans("googlesheet::messages.edit_connection") }}</a></li>
                                <li>
                                    <a class="dropdown-item list-action-single"
                                        link-method="POST"
                                        link-confirm-url="{{ action('\Wikko\Googlesheet\Controllers\GoogleController@deleteConfirm', ['uids' => $list->id]) }}"
                                        href="{{ action('\Wikko\Googlesheet\Controllers\GoogleController@delete', ['uids' => $list->id]) }}">
                                        <span class="material-symbols-rounded me-2">delete</span> {{ trans('messages.delete') }}
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
    @include('elements/_per_page_select', ["items" => $lists])


    <script>
        var ListsList = {
            clonePopup: new Popup(),
            copyPopup: null,

            getCopyPopup: function() {
                if (this.copyPopup === null) {
                    this.copyPopup = new Popup();
                }

                return this.copyPopup;
            },

            getClonePopup: function() {
                if (this.clonePopup === null) {
                    this.clonePopup = new Popup();
                }

                return this.clonePopup;
            },
        }

        $('.clone-for-users').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            // load popup
            ListsList.getClonePopup().load({
                url: url
            });
        });

        $('.copy-list-button').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            ListsList.getCopyPopup().load({
                url: url
            });
        });
    </script>

@elseif (!empty(request()->keyword))
    <div class="empty-list">
        <span class="material-symbols-rounded">auto_awesome</span>
        <span class="line-1">
            {{ trans('messages.no_search_result') }}
        </span>
    </div>
@else
    <div class="empty-list">
        <span class="material-symbols-rounded">auto_awesome</span>
        <span class="line-1">
            {{ trans('googlesheet::messages.empty_connection') }}
        </span>
    </div>
@endif
