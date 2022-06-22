@if ($crud->hasAccess('list'))
    @php($route = $crud->get('import_route'))

    <a href="{{ $route }}" class="btn btn-xs btn-default"><i class="fa fa-ban"></i> {{ __('backpack-async-export::import.buttons.import') }}</a>
@endif
