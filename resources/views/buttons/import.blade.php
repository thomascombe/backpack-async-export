@if ($crud->hasAccess('list'))
    @php($route = $crud->get('import_route'))
    @php($url = url($crud->route . '/' . $route))

    <a href="{{ $url }}" class="btn btn-secondary">
        <i class="la la-upload"></i> @lang('backpack-async-export::import.buttons.import')
    </a>
@endif
