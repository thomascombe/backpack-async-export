@extends('backpack::layouts.top_left')

@section('header')
    <section class="content-header">
        <h1>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
            <small>{!! $crud->getSubheading() ?? trans('backpack-async-export::admin.operation.import') . ' ' . $crud->entity_name !!}.</small>
        </h1>
        @include('backpack::inc.breadcrumbs')
    </section>
@endsection

@section('content')
    @if ($crud->hasAccess('list'))
        <a href="{{ url($crud->route) }}" class="hidden-print">
            <span class="fa fa-angle-double-left"></span>
            {{ trans('backpack::crud.back_to_all') }}
            <span>{{ $crud->entity_name_plural }}</span>
        </a>
    @endif

    <div class="row m-t-20">
        <div class="{{ $crud->getEditContentClass() }}">
            <!-- Default box -->

            @include('crud::inc.grouped_errors')

            <form method="post" action="{{ url($crud->route . '/import') }}" enctype="multipart/form-data">
                @csrf

                <div class="col-md-12">
                    <div class="">
                        <!-- load the view from the application if it exists, otherwise load the one in the package -->
                        @if(view()->exists('backpack-async-export::vendor.backpack.crud.form_content'))
                            @include('backpack-async-export::vendor.backpack.crud.form_content', ['fields' => $fields, 'action' => 'edit'])
                        @elseif(view()->exists('vendor.backpack.crud.form_content'))
                            @include('vendor.backpack.crud.form_content', ['fields' => $fields, 'action' => 'edit'])
                        @else
                            @include('crud::form_content', ['fields' => $fields, 'action' => 'edit'])
                        @endif

                    </div>

                    <div class="form-group">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-success">
                                <span class="fa fa-upload" role="presentation" aria-hidden="true"></span>
                                <span
                                    data-value="{{ trans('backpack-async-export::admin.button.import') }}">{{ trans('backpack-async-export::admin.button.import') }}</span>
                            </button>
                        </div>

                        <a href="{{ $crud->hasAccess('list') ? url($crud->route) : url()->previous() }}"
                           class="btn btn-default">
                            <span class="fa fa-ban"></span>
                            {{ trans('backpack::crud.cancel') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
