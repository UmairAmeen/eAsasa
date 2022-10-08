@extends('layout')
<style>
#report_links {color: green;font-weight: bolder;font-size: 22px;}/* link */
#report_links:hover {color: black;}/* mouse over link */
#report_span {font-size: 18px;}/* selected link */
</style>
@section('header')
    <div class="page-header clearfix">
        <h1><i class="fas fa-chart-pie"></i> Reports</h1>
    </div>
@endsection
@section('content')
    <div class="col-md-12" style="background-color: white">
        @if(isset($request->error))
            <div class="alert alert-danger" role="alert">{{$request->error}}</div>
        @endif
        @foreach ($chunked_array_report as $reports)
        <div class="row">
        @foreach ($reports as $reportName => $reportList)
        {{-- <div class="col-md-6" style="height: {{$reportList['height']}};"> --}}
            <div class="col-md-6">
                <h3><i class="fa fa-angle-right"></i> {{ $reportName }}</h3>
                <ul>
                @foreach ($reportList['list'] as $report)
                    <li>
                        <a id="report_links" href="{{ $report['url'] }}">{{ $report['name'] }}<i
                                class="{{ $report['icon'] }}"></i></a><br>
                        <span id="report_span">{{ $report['text'] }}</span>
                    </li>
                @endforeach
                </ul>
            </div>
        @endforeach
        </div>
        @endforeach
    </div>
@endsection
