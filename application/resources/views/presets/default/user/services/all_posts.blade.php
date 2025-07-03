@extends($activeTemplate.'layouts.master')
@section('content')
<div class="body-wrapper">
    <div class="table-content">
        <div class="row gy-4 mb-4">
            @forelse($services as $service)
            @if(!in_array($service->id, $viewed))
            <div class="col-xl-3 col-lg-4 col-md-4 col-12">
                <div class="dash-card">
                    <a href="{{ route('user.post.details', ['slug' => slug($service->name), 'id' => $service->id]) }}" class="d-flex justify-content-between">
                        <div>
                            <p>{{__($service->name)}}</p>
                            <p class="text--base">@lang('View') <i class="fa-solid fa-arrow-right"></i></p>
                        </div>
                        <div class="icon service_icon my-auto">
                          <img src="{{ getImage(getFilePath('category').'/'.@$service->category->image)}}" alt="@lang('image')">
                        </div>
                    </a>
                </div>
            </div>
            @endif
            @empty
            <h2 class="text-center">{{__($emptyMessage)}}</h2>
            @endforelse
        </div>
    </div>
</div>
@endsection
