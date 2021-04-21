@if ($crud->hasAccess('list'))
    @php($exports = $crud->get('exports', ['default' => null]))
    @foreach($exports as $export => $exportsName)
        <a href="{{ url($crud->route.'/'.config('backpack-async-export.admin_route')) }}?{{ \Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces\MultiExportableCrud::QUERY_PARAM }}={{ $export }}" class="btn btn-xs btn-default"><i class="fa fa-ban"></i> {{ __('backpack-async-export::export.buttons.exports') }}@if (!empty($exportsName)) ({{ $exportsName }})@endif</a>
    @endforeach
@endif
