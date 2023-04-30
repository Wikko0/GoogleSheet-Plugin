<p>{{ trans('messages.delete_list_confirm_warning') }}</p>
<ul class="modern-listing">
    @foreach ($lists->get() as $list)
        <li>
            <div class="d-flex">
                <span class="material-symbols-rounded fs-5 text-danger me-3" style="margin-top: -5px;">error_outline</span>
                <div>
                    <h5 class="mb-1" class="text-danger">{{ $list->connection_name }}</h5>
                </div>
            </div>
        </li>
    @endforeach
</ul>
