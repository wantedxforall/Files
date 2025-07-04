@extends($activeTemplate.'layouts.master')
@section('content')
<div class="body-wrapper">
    <div class="table-content">
        <div class="row justify-content-end mb-3">
            <div class="col-lg-4 col-md-8 col-12 text-end">
                <a class="btn btn--base btn--sm" href="{{ route('user.service.index') }}">
                    <i class="fas fa-backward"></i> @lang('Back')
                </a>
            </div>
        </div>
        <div class="list-card">
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>@lang('Screenshot')</th>
                                <th>@lang('Submitted At')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proofs as $proof)
                            <tr>
                                <td data-label="@lang('Screenshot')">
                                    <img src="{{ asset('assets/images/proofs/'.$proof->screenshort) }}" alt="@lang('screenshot')" style="max-width: 100px">
                                </td>
                                <td data-label="@lang('Submitted At')">{{ showDateTime($proof->created_at) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%" class="text-center">@lang('No proofs found')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-5">
                    @if($proofs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($proofs) }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection