@extends('layout')
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.0/fullcalendar.min.css" integrity="sha512-LPrOACFtmEJvoUjBN9WGv1ztStvmzPXlY+tmyZKmDBlKTdDXBa3M0RPVRNfWNp2vP0TRUoxZrv2AzdgJVhS7uw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
@section('content')

    <div class="page-header clearfix">
        <div class="align-items-center">
            <div class="mr-auto" style="display: flex; justify-content: space-between; align-items: center; ">
                <div>
                    <h1 class="m-subheader__title m-subheader__title--separator">
                        Human Resource
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <div class="content-panel">
        <div class="m-portlet">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <!-- <span class="m-portlet__head-icon">
                            <i class="flaticon-users"></i>
                        </span> -->
                        <h3 class="m-portlet__head-text">
                            Public Holidays
                        </h3>

                    </div>
                </div>
                <div class="m-portlet__head-tools">
                    <ul class="m-portlet__nav">
                        <li class="m-portlet__nav-item">
                            <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                 data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="#" data-toggle="modal" data-target="#m_modal_4"
                                   class="m-portlet__nav-link btn btn-lg btn-secondary  m-btn m-btn--outline-2x m-btn--air m-btn--icon m-btn--icon-only m-btn--pill  m-dropdown__toggle">
                                    <i class="la la-plus"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>


            <div class="m-portlet__body">

                {{--  <center>
                 <form id="month_ch">
                            <input value="{{date('Y-m', strtotime($start_month))}}" type="month" name="month_year" onchange="$('#month_ch').submit()">

                            </form>
                            <small>
                     {{date_to_str($start_month)}} to {{date_to_str($end_month)}}
                 </small>

                 </center> --}}

                <div id="holiday_calendar"></div>
                <div class="table-responsive">
                    <table id="example" class="table table-condensed table-striped"
                           style="width:100%;text-align: center">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>From</th>
                            <th>To</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($holiday as $holi)
                            <tr>
                                <td>{{$holi->id}}</td>
                                <td>{{$holi->occassion}}</td>
                                <td>{{$holi->from}}</td>
                                <td>{{$holi->to}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
    {{-- modal begin --}}
    <div class="modal fade" id="m_modal_4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Holiday</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form role="form" method="POST" action="{{url('/hr/add_holiday')}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="recipient-name" class="form-control-label">From</label>
                            <input class="form-control m-input" type="date" name="from" value="2018-10-03">
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="form-control-label">To</label>
                            <input class="form-control m-input" type="date" name="to" value="2018-10-03">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="form-control-label">Occasion</label>
                            <input class="form-control" name="occassion" type="text">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Holiday</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- modal ends --}}
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.0/moment.min.js" integrity="sha512-Izh34nqeeR7/nwthfeE0SI3c8uhFSnqxV0sI9TvTcXiFJkMd6fB644O64BRq2P/LA/+7eRvCw4GmLsXksyTHBg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.0/fullcalendar.min.js" integrity="sha512-Za1rKe2s67q5DwADR4ciRWJKTA7rM4mAzmwyVZZq6UwaJpsXxfrPhGKAwTFNbxI4jpVWRhW33fqrM8KX/xCgEg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript">
        function getColorsForEvent(type) {
            switch (type) {
                case 'present':
                    return '#9ccc65';
                    break;

                case 'absent':
                    return '#ff2e7c';
                    break;

                case 'holiday':
                    return '#80DEEA';
                    break;

                case 'leave':
                    return '#FFC107';
                    break;

                default:
                    return "#000";
                    break;
            }
        }
        $('#holiday_calendar').fullCalendar({
            header: {left: 'prev,next today', center: 'title', right: 'listDay,listWeek,month'},
            eventClick: function (event, element) {
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        var ontrue = function () {
                            swal(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            )
                            $('#holiday_calendar').fullCalendar("removeEvents", event._id);
                        }
                        fetch("{{url('/hr/delete_holiday')}}/" + event.id, false, "DELETE", "JSON", ontrue, false, false, true);
                    }
                })
            },
            selectable: true,
            selectHelper: true,
            select: function (startDate, endDate) {
                swal({
                    title: 'Reason for Holiday',
                    input: 'text',
                    inputAttributes: {autocapitalize: 'off'},
                    showCancelButton: true,
                    confirmButtonText: 'Add Holiday',
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !swal.isLoading()
                }).then((result) => {
                    if (result.value) {
                        var eventData;
                        eventData = {
                            title: result.value,
                            start: startDate,
                            end: endDate,
                            color: getColorsForEvent("holiday")
                        };
                        // debugger
                        var data = {"from": startDate.format(), "to": endDate.format(), "occassion": result.value};
                        var ontrue = function (data) {
                            $('#holiday_calendar').fullCalendar('renderEvent', eventData, true);
                        };
                        fetch("{{url('/hr/add_holiday')}}", data, "POST", "JSON", ontrue, false, false, true);
                    }
                })
            },
            eventLimit: true, // allow "more" link when too many events
            events: [
                @foreach($holiday as $holi) {
                    id: {{$holi->id}},
                    title: '{{$holi->occassion}}',
                    start: '{{$holi->from}}',
                    end: '{{$holi->to}}',
                    color: getColorsForEvent('holiday'),
                },
                @endforeach
            ],
        });
    </script>
@endsection