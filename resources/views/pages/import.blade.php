@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
      trans('backpack::crud.admin') => backpack_url('dashboard'),
      $crud->entity_name_plural => url($crud->route),
      trans('backpack-async-export::admin.operation.import') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none" bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</h1>
        <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">{!! $crud->getSubheading() ?? trans('backpack-async-export::admin.operation.import').' '.$crud->entity_name !!}.</p>
        @if ($crud->hasAccess('list'))
            <p class="mb-0 ms-2 ml-2" bp-section="page-subheading-back-button">
                <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
            </p>
        @endif
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
                    'vendor.backpack.crud.form_content',
                    'crud::form_content'
                  ],
                  [
                    'fields' => $crud->fields(),
                    'action' => 'edit',
                  ]
                )

                <div class="d-none" id="parentLoadedAssets">{{ json_encode(Basset::loaded()) }}</div>
                <div id="saveActions" class="form-group my-3">
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
