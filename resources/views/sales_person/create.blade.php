@extends('layout')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css"
        rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i> Add Sales Person </h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-10">
            <form action="{{ route('salesPerson.store') }}" method="POST">
                <div id="log" class=""></div>
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" class="form-control" placeholder="Sales Person Name">
                    <small id="emailHelp" class="form-text text-muted">This is required</small>
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="tel" name="phone" class="form-control" placeholder="Sales Person Mobile Number">
                </div>
                @if (is_admin())
                <div class="form-group">
                    <label>Commission:</label>
                    <input type="text" name="commission" class="form-control" placeholder="commission">
                </div>
                @endif
                <div class="form-group">
                    <label>Address:</label>
                    <input type="text" name="address" class="form-control" placeholder="Sales Person Address">
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">


                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a class="btn btn-link pull-right" href="{{ route('salesPerson.index') }}"><i
                            class="glyphicon glyphicon-backward"></i> Back</a>
                </div>
            </form>

        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/js/bootstrap-datepicker.min.js"></script>
    <!--custom switch-->
    <script src="{{ asset('assets/js/bootstrap-switch.js') }}"></script>

    <script>
        $('.date-picker').datepicker({});
        $(document).ready(function() {
            $("[data-toggle='switch']").wrap('<div class="switch" />').parent().bootstrapSwitch();

        });
    </script>
@endsection
