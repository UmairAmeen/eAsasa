@extends('layout')
@section('css')
    <link href="{{asset('assets/js/bootstrap-datetimepicker/css/datetimepicker.css')}}" rel="stylesheet">
    <link href="{{ asset('base/style.bundle.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div class="m-subheader ">
        <div class="align-items-center">
            <div class="mr-auto">
                <h3 class="m-subheader__title m-subheader__title--separator">
                    Human Resource
                </h3>
                <div style="    float: right;padding: 5px 0px 0px 0px;">
                    <input style="    width: 277px; border: none;border-radius: 72px;padding: 5px 7px 5px 7px;"
                           type="search" name="search" placeholder="Search">
                </div>
            </div>

        </div>
    </div>
    <br>
    <div class="m-content">
        <div class="m-portlet">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <!-- <span class="m-portlet__head-icon">
                            <i class="flaticon-users"></i>
                        </span> -->
                        <h3 class="m-portlet__head-text">
                            Add Bonus (#{{$employee->id}}  {{$employee->name}})
                        </h3>
                    </div>
                </div>
            </div>
            <center>
                <h4>
                    <form id="month_ch">
                        <input value="{{date('Y-m', strtotime($start_month))}}" type="month" name="month_year"
                               onchange="$('#month_ch').submit()">

                    </form>
                </h4>

                {{-- <h4>{{date('F Y', strtotime($start_month))}}</h4> --}}
                <small>{{date_to_str($start_month)}} to {{date_to_str($end_month)}}ur</small>

            </center>
            <div class="m-portlet__body">
                @if ($errors->any())
                    {{ implode('', $errors->all(':message')) }}
                @endif

                @if (count($employeebonus) > 0)
                    @foreach ($employeebonus as $key => $log)
                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>Bonus # {{$key + 1}}</strong>
                            </label>

                            <input class="form-control m-input" type="hidden" name="bonus_id[]"
                                   value="abs{{($log->id) }}">
                            <div class="col-lg-2 col-form-label">
                                <label>Type</label>
                                <select name="type_of_bonus[]" class="form-control m-input">
                                    <option value="bonus" {{$log->bonus>0 ? 'selected' : ''}}>Bonus</option>
                                    <option value="deduction" {{$log->bonus<0 ? 'selected' : ''}}>Deduction</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-form-label">
                                <label>Amount</label>
                                <input class="form-control m-input" type="text" name="bonus[]"
                                       value="{{ abs($log->bonus) }}">

                            </div>
                            <div class="col-lg-3 col-form-label">
                                <label>Reason</label>
                                <input class="form-control m-input" type="text" name="bonus_reason[]"
                                       value="{{ $log->description }}">

                            </div>
                            <div class="col-lg-3 col-form-label">
                                <label>Date</label>
                                <input class="form-control m-input" type="date" name="bonus_date[]"
                                       value="{{ $log->date }}">

                            </div>
                            <div class="col-lg-1 col-form-label" style="line-height: 3">
                                <a onclick="Removenow(this,{{$log->id}})"><i class="la la-trash"></i></a>
                            </div>
                        </div>
                    @endforeach
                @endif
                <form class="form-horizontal" role="form" method="POST" action="{{url('/hr/update_bonus')}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="{{ $employee->id }}">
                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label">
                            <strong>Add new Bonus</strong>
                        </label>
                        <input type="hidden" name="bonus_id[]" value="0">
                        <div class="col-lg-2 col-form-label">
                            <label>Type</label>
                            <select name="type_of_bonus[]" class="form-control m-input">
                                <option value="bonus">Bonus</option>
                                <option value="deduction">Deduction</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-form-label">
                            <label>Amount</label>
                            <input class="form-control m-input" type="text" placeholder="Bonus" name="bonus[]">

                        </div>
                        <div class="col-lg-3 col-form-label">
                            <label>Reason</label>
                            <input class="form-control m-input" type="text" name="bonus_reason[]"
                                   placeholder="Reason of Bonus" value="">

                        </div>
                        <div class="col-lg-3 col-form-label">
                            <label>Date</label>
                            <input class="form-control m-input m_datepicker" type="text" name="bonus_date[]"
                                   placeholder="Date of Bonus" value="{{date('Y-m-d', strtotime($start_month))}}">

                        </div>
                    </div>
            </div>
            <div class="m-portlet__foot">
                <div class="row align-items-center">
                    <div class="col-lg-6 m--valign-middle">
                        <a href="{{url()->previous()}}" class="m-link m--font-bold">
                            Back
                        </a>
                    </div>
                    <div class="col-lg-6 m--align-right">
							<span class="m--margin-left-10">
								<button type="submit" class="btn btn-success">
									Submit
								</button>
							</span>
                    </div>
                </div>
            </div>
            </form>

        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{asset('assets/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js')}}"></script>
    <script type="text/javascript">
        $('.m_datepicker').datepicker({
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        });

        function Removenow(ele, id) {

            swal({
                animation: false,
                title: 'Are you sure?',
                text: "You wish to delete bonus",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1e8fe1',
                cancelButtonColor: '#ffffff',
                confirmButtonText: 'Delete',
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.value) {

                    var onsucess = function (data) {
                        $(ele).parent().parent().remove();
                        toastr.success('Deleted Successfully.', 'Success Alert', {timeOut: 5000});
                        window.setTimeout(function () {
                            location.reload()
                        }, 1000);
                    }
                    ajaxcall("{{url('hr/delbonus')}}/" + id, "DELETE", false, false, onsucess);
                }
            });
        }
    </script>
@endsection