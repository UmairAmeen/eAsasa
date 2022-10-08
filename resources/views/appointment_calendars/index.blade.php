@extends('layout')
@section('header')
    
    <link rel="stylesheet" href="{{asset('vendor/fullcalendar/fullcalendar.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('vendor/fullcalendar/fullcalendar.print.css')}}" media="print" />
    <div class="page-header clearfix">
        <h1>
            <i class="glyphicon glyphicon-calendar"></i> Calendar &amp; Notifications
            <!-- <a class="btn btn-success pull-right" href="{{ route('appointment_calendars.create') }}"><i class="glyphicon glyphicon-plus"></i> Create</a> -->
        </h1>

    </div>
@endsection

@section('content')
    <div class="row mt">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="showback">
                {!! $calendar->calendar() !!}
                

            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="showback">
            <form action="{{route('appointment_calendars.store')}}">
                <div id="log"></div>
                <div class="form-group">
                    <label>Name: </label>
                <input type="text" class="form-control" name="title" required="required" minlength="3">
                    
                </div>

                <div class="form-group">
                    <label>Date: </label>
                <input type="date" class="form-control" name="start" required="required" value="{{date('Y-m-d')}}">
                    
                </div>
<!--                 <div class="form-group">
                    <label>End DateTime: </label>
                <input type="datetime-local" class="form-control" name="end" required="required">
                    
                </div> -->

                <div class="form-group">
                    <label>Color: </label>
                <input type="color" class="form-control" name="background_color" value="#2196f3" required="required">
                    
                </div>

                <input type="submit" name="submit" value="Add Appointment" class="btn btn-success">

            </form>
        </div>
    </div>
    </div>
    <br><br><br>
    <div class="row mt">
        
        <div  class="col-md-12">
            <div class="showback">
            <table id="appointment_table" class="table">
                <thead>
                    <tr>
                    <th>Id</th>
                    <th>Title</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>option</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {

    $('#appointment_table').dataTable( {
  "ajax": "/appointment_datatable",
  "processing": true,
  "serverSide": true,
  "order": [[0,'desc']],
   "columns": [
            { "data": "id", "orderable": true, "searchable": true},
            {"data":'title'},
            { "data": "start" },
            { "data": "end" },
            { "data": "options", "orderable": false, "searchable": false }

        ],
        "fnDrawCallback": function (oSettings) {
          init();
        }
  });
});
</script>
   <script src="{{asset('vendor/fullcalendar/moment.min.js')}}"></script>
   <script src="{{asset('vendor/fullcalendar/fullcalendar.min.js')}}"></script>
   {!! $calendar->script() !!}
@endsection