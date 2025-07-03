@extends($activeTemplate.'layouts.master')
@section('content')

<div class="body-wrapper">
    <div class="table-content">
        <div class="row justify-content-end mb-3">
            <div class="col-lg-4 col-md-8 col-12 text-end">
                <a class="btn btn--base btn--sm" href="{{route('user.service.index')}}"> <i class="fas fa-backward"></i>
                    @lang('Back') </a>
            </div>
        </div>
        <div class="body-area">
            <div class="form-body">
                <form role="form" method="POST" action="{{route('user.service.update',$service->id)}}" enctype="multipart/form-data">
                    @csrf

                    <div class="row gy-4">

                        <div class="form-group col-md-6 col-lg-6 col-xl-4">
                            <label class="form--label">@lang('Name') <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{$service->name}}" class="form--control"
                                placeholder="@lang('Name')" required>
                        </div>

                        <div class="form-group col-md-6 col-lg-6 col-xl-4">
                            <label class="form--label">@lang('Category') <span class="text-danger">*</span></label>
                            <select class="form--control" name="category" required>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $service->category_id == $category->id ? 'selected' : '' }}>
                                    {{ __($category->name) }}
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6 col-lg-6 col-xl-4">
                            <label class="form--label">@lang('Link') <span class="text-danger">*</span></label>
                            <input type="text" name="link" id="link" value="{{$service->link}}" class="form--control"
                                placeholder="@lang('Link')" required>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="form--label">@lang('Link') <span class="text-danger">*</span></label>
                            <textarea class="trumEdit" name="link_description" cols="30" rows="10"
                                placeholder="@lang('Enter your link description')">
                                @php echo $service->link_description; @endphp
                            </textarea>
                        </div>
                    </div>

                    <div class="form-group col-md-12 mt-3 text-end">
                        <button type="submit" class="btn btn--base">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

