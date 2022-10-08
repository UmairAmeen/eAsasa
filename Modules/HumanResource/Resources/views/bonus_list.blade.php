@extends('layout')
@section('header')
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

                <div style="    float: right;padding: 5px 0px 0px 0px;">
                    <input id="myInput" style="width: 277px; border: none;border-radius: 72px;padding: 5px 7px 5px 7px;"
                           type="search" name="search" placeholder="Search">
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
                            Bonus
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
                <center style="margin-bottom: 30px;">
                    <form id="month_ch">
                        <input value="{{date('Y-m', strtotime($start_month))}}" type="month" name="month_year"
                               onchange="$('#month_ch').submit()">

                    </form>
                    <small>
                        {{date_to_str($start_month)}} to {{date_to_str($end_month)}}
                    </small>

                </center>
                <div class="table-responsive">
                    <table id="example" class="table table-condensed table-striped"
                           style="width:100%;text-align: center">
                        <thead>
                        <tr>
                            <th>Employee #</th>
                            <th>Employe Name</th>
                            <th>Bonus</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($employee_bonus as $b)
                            <tr>
                                <td>{{$b->employee_id}}</td>
                                <td>{{$b->employee->name}}</td>
                                <td>{{$b->sb}}</td>
                                <td>
                                    <button class="form-control" style="cursor: pointer;width:100px;margin: auto;"><a
                                                href="{{url('hr/bonus')."/".$b->employee_id}}?month_year={{date('F Y', strtotime($start_month))}}">Add
                                            Bonus</a></button>
                                </td>
                            </tr>
                        @endforeach
                        @foreach($employee as $emp)
                            <tr>
                                <td>{{$emp->id}}</td>
                                <td>{{$emp->name}}</td>
                                <td>-</td>
                                <td>
                                    <button class="form-control" style="cursor: pointer;width:100px;margin: auto;"><a
                                                href="{{url('hr/bonus')."/".$emp->id}}?month_year={{date('F Y', strtotime($start_month))}}">Add
                                            Bonus</a></button>
                                </td>
                            </tr>
                            @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
    <div class="modal fade" id="m_modal_4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Add Bonus
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">
                                                &times;
                                            </span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{url('/hr/add_bonus')}}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label for="recipient-name" class="form-control-label">
                                Employe:
                            </label>
                            <select name="employee_name" class="form-control m-input">
                                @foreach ($employs as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message-text" class="form-control-label">
                                Bonus:
                            </label>
                            <input class="form-control" name="bonus">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="form-control-label">
                                Date:
                            </label>
                            <input class="form-control m-input" type="date" name="month" placeholder="Date of bonus">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="form-control-label">
                                Reason:
                            </label>
                            <textarea class="form-control" name="reason_bonus"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="Submit" class="btn btn-primary">
                        Add bonus
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection