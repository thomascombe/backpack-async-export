@if ($crud->hasAccess('import'))
    @php($route = $crud->get('import_route'))

    <a href="{{ $route }}" class="btn btn-secondary">
        <i class="la la-upload"></i> @lang('backpack-async-export::import.buttons.import')
    </a>
@endif
