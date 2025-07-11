@extends('admin.layouts.app')
@section('panel')

<div class="row mb-none-30">
    <div class="col-lg-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.plan.update',$plan->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <label class="fw-bold">@lang('Status')</label>
                                <label class="switch m-0">
                                    <input type="checkbox" class="toggle-switch" name="status" {{ $plan->status ?
                                    'checked' : null }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="name" class="font-weight-bold">@lang('Name')</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{$plan->name}}" required>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="price" class="font-weight-bold">@lang('Price')</label>
                                <input style="any" type="number" name="price" id="price" class="form-control "value="{{$plan->price}}"
                                     required>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="credits" class="font-weight-bold">@lang('Credits')</label>
                                <input type="number" name="credits" id="credits" class="form-control "value="{{$plan->credits}}"
                                     required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label class="font-weight-bold">@lang('Content')</label>
                            <div class="content-fields">
                                @if(@$plan->content)
                                @foreach(json_decode($plan->content) ?? [] as $key => $value)
                                        <div class="row mb-2 content-field">
                                            <div class="col-10">
                                                <div class="form-group">
                                                    <input type="text" name="contents[{{ $key }}]" id="content_{{ $key }}" value="{{ $value }}"
                                                        class="form-control" placeholder="@lang('Content')">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <button type="button" class="btn btn--danger text--white removePlanContent w-100"><i class="la la-times"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="col-12">
                                <div id="planContent"></div>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="button" class="btn btn--primary addPlanContent"><i class="fa fa-plus"></i>@lang('Add New')</button>
                            </div>
                        </div>
                    </div>

                    <div class="row text-end">
                        <div class="col-lg-12 ">
                            <div class="form-group float-end p-3">
                                <button type="submit" class="btn btn--primary btn-block btn-lg"> @lang('Update')</button>
                            </div>
                        </div>
                    </div>
                 </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
<a href="{{route('admin.plan.index')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i
        class="las la-angle-double-left"></i>@lang('Go Back')</a>
@endpush

@push('script')

<script>

    (function ($) {

        "use strict";

        var fileAdded = 0;
        $('.addPlanContent').on('click', function () {

            $("#planContent").append(`
                    <div class="row">
                        <div class="col-10">
                            <div class="form-group">
                             <input type="text" name="contents[]" id="content" value="{{ old('contents.0') }}" class="form-control" placeholder="@lang('Content')">
                            </div>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn--danger text--white planContentDelete w-100"><i class="la la-times ms-0"></i></button>
                        </div>
                    </div>
                `)
        });

        $(document).on('click', '.planContentDelete', function () {
            fileAdded--;
            $(this).closest('.row').remove();
        });

          // Remove content field
            $(document).on('click', '.removePlanContent', function() {
                $(this).closest('.content-field').remove();
            });

    })(jQuery);
</script>

@endpush

