@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="fa fa-cube"></i> Utilities
        </h1>

    </div>
@endsection

@section('content')
    <div class="content-panel">
        @if (session('message'))
            <div id="status-message" class="alert alert-success">{{ session('message') }}</div>
        @endif
        <h3>Click Relevant button to perform desire Function</h3>
        <div class="container">
            <div class="col-sm-12" style="margin-top:3%">
                <div class="col-sm-3">
                    <a href="{{ url('clearCache') }}" class="btn btn-sm btn-success">Clear Cache</a>
                </div>
                <div class="col-sm-3">
                    <a href="{{ url('clearViews') }}" class="btn btn-success">Clear Views</a>
                </div>
            </div>
        </div>
        <h3>Add Fake Data For Testing</h3>
        <div class="container">
            <div class="col-sm-12" style="margin-top:3%">
                <input type="number" id="fake_data_count" style="width: 80%" class="form-control"><br>
                @foreach ($fake_data as $key => $data)
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span>
                                <button id="{{ $key }}" class="btn btn-success">{{ $data }}</button>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endsection
    @section('scripts')
        <script type="text/javascript">
            $(document).ready(function() {
                setTimeout(function() {
                    $('#status-message').slideUp(400);

                }, 2000);
            });
            $("button").click(function() {
                $.ajax({
                    url: "/addFakeData",
                    type: 'POST',
                    data: {
                        'name': $(this).attr('id'),
                        'count': $(fake_data_count).val()
                    }
                }).done(function(data) {
                    window.location = "/"+data.location+"s";
                }).error(function(d) {
                    alert("Fail");
                });
            });
        </script>
    @endsection
