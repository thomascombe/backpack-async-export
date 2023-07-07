@if ($crud->hasAccess('export'))
    @php($exports = $crud->get('exports', ['default' => null]))
    @php($url = url($crud->route . '/' . config('backpack-async-import-export.admin_export_route')))
    @foreach($exports as $export => $exportsName)
        <a href="{{ $url }}?{{ Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces\MultiExportableCrud::QUERY_PARAM }}={{ $export }}" class="btn btn-secondary">
            <i class="la la-download"></i> @lang('backpack-async-export::export.buttons.exports') @if (!empty($exportsName)) ({{ $exportsName }})@endif
        </a>
    @endforeach
@endif
