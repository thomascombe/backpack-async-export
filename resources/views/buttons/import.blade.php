@if ($crud->hasAccess('import'))
    @php($route = $crud->get('import_route'))
    @php($url = url('/' . $route))

    <a href="{{ $url }}" class="btn btn-secondary">
        <span>
            <i class="la la-upload"></i>
            @lang('backpack-async-export::import.buttons.import')
        </span>
    </a>
@endif
