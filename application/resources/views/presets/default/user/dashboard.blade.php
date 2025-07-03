@extends($activeTemplate.'layouts.master')
@section('content')
<div class="body-wrapper">
    <div class="table-content">
        <div class="row gy-4 mb-4">

            <div class="col-xl-3 col-lg-4 col-md-4 col-12">
                <div class="dash-card">
                    <a href="javascript:void(0)" class="d-flex justify-content-between">
                        <div>
                            <div>
                                <p>@lang('Total Balance')</p>
                            </div>
                            <div class="content">
                                <span class="text-uppercase">{{$general->cur_sym}}{{showAmount(auth()->user()->balance)}}</span>
                            </div>

                        </div>
                        <div class="icon my-auto">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-4 col-12">
                <div class="dash-card">
                    <a href="{{route('user.plan')}}" class="d-flex justify-content-between">
                        <div>
                            <div>
                                <p>@lang('Total Credits')</p>
                            </div>
                            <div class="content">
                                <span class="text-uppercase">#{{auth()->user()->credits}}</span>
                            </div>

                        </div>
                        <div class="icon my-auto">
                            <i class="fa-regular fa-credit-card"></i>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-4 col-12">
                <div class="dash-card">
                    <a href="{{route('user.service.index')}}" class="d-flex justify-content-between">
                        <div>
                            <div>
                                <p>@lang('Total Posts')</p>
                            </div>
                            <div class="content">
                                <span class="text-uppercase">{{$widget['total_services']}}</span>
                            </div>
                        </div>
                        <div class="icon my-auto">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-4 col-12">
                <div class="dash-card">
                    <a href="{{route('user.service.pending')}}" class="d-flex justify-content-between">
                        <div>
                            <div>
                                <p>@lang('Pending Posts')</p>
                            </div>
                            <div class="content">
                                <span class="text-uppercase">{{$widget['pending_services']}}</span>
                            </div>
                        </div>
                        <div class="icon my-auto">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                    </a>
                </div>
            </div>

        </div>


        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="chart">
                    <div class="chart-bg">
                        <h4>@lang('Monthly Deposits Reports')</h4>
                        <div id="account-chart"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection


@push('script')
<script src="{{asset('assets/admin/js/apexcharts.min.js')}}"></script>
<script>
    (function () {
    'use strict';
        var options = {
            chart: {
                type: 'area',
                stacked: false,
                height: '310px'
            },
            stroke: {
                width: [0, 3],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%'
                }
            },
            colors: ['#4430b5', '#ee6f11'],
            series: [{
                name: '@lang("Deposits")',
                type: 'column',
                data: @json($depositsChart['values'])
    }],
    fill: {
        opacity: [0.85, 1],
                },
    markers: {
        size: 0
    },
    xaxis: {
        type: 'text'
    },
    yaxis: {
        min: 0
    },
    tooltip: {
        shared: true,
            intersect: false,
                y: {
            formatter: function (y) {
                if (typeof y !== "undefined") {
                    return "$ " + y.toFixed(0);
                }
                return y;

            }
        }
    },
    legend: {
        labels: {
            useSeriesColors: true
        },
        markers: {
            customHTML: [
                function () {
                    return ''
                },
                function () {
                    return ''
                }
            ]
        }
    }
            }
    var chart = new ApexCharts(
        document.querySelector("#account-chart"),
        options
    );
    chart.render();
        }) ();


</script>
@endpush

