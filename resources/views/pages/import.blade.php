@extends(backpack_view('blank'))

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
            <small>{!! $crud->getSubheading() ?? trans('backpack-async-export::admin.operation.import') . ' ' . $crud->entity_name !!}
                .</small>

            @if($crud->hasAccess('list'))
                <small>
                    <a href="{{ url($crud->route) }}" class="d-print-none font-sm">
                        <i class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i>
                        @lang('backpack::crud.back_to_all')
                        <span>{{ $crud->entity_name_plural }}</span>
                    </a>
                </small>
            @endif
        </h2>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="{{ $crud->getEditContentClass() }}">
            @include('crud::inc.grouped_errors')

            <form action="{{ url($crud->route . '/import') }}" enctype="multipart/form-data" method="post">
                @csrf

                @includeFirst(
                  [
                    'backpack-async-export::vendor.backpack.crud.form_content',
                    'vendor.backpack.crud.form_content',
                    'crud::form_content'
                  ],
                  [
                    'fields' => $fields,
                    'action' => 'edit',
                  ]
                )

                <div class="d-none" id="parentLoadedAssets">{{ json_encode(Assets::loaded()) }}</div>
                <div id="saveActions" class="form-group">
                    <div class="btn-group" role="group">
                        <button type="submit" class="btn btn-success">
                            <span class="la la-save" role="presentation" aria-hidden="true"></span> &nbsp;
                            <span
                                data-value="@lang('backpack-async-export::admin.button.import')">@lang('backpack-async-export::admin.button.import')</span>
                        </button>
                    </div>

                    @if(!$crud->hasOperationSetting('showCancelButton') || $crud->getOperationSetting('showCancelButton') == true)
                        <a href="{{ $crud->hasAccess('list') ? url($crud->route) : url()->previous() }}" class="btn btn-default">
                            <span class="la la-ban"></span> &nbsp;@lang('backpack::crud.cancel')
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
