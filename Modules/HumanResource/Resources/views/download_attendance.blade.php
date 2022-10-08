@extends('layouts.master')

@section('content')
    <div class="m-subheader ">
        <div class="align-items-center">
            <div class="mr-auto">
                <h3 class="m-subheader__title m-subheader__title--separator">
                    Human Resource
                </h3>
                <ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
                    <li class="m-nav__item m-nav__item--home">
                        <a href="#" class="m-nav__link m-nav__link--icon">
                            <i class="m-nav__link-icon la la-home"></i>
                        </a>
                    </li>
                    <li class="m-nav__separator">
                        -
                    </li>
                    <li class="m-nav__item">
                        <a href="{{ url('home') }}" class="m-nav__link">
                            <span class="m-nav__link-text">
                                Dashboard
                            </span>
                        </a>
                    </li>
                    <li class="m-nav__separator">
                        -
                    </li>
                    <li class="m-nav__item">
                        <a href="{{ url('hr') }}" class="m-nav__link">
                            <span class="m-nav__link-text">
                                Human Resource
                            </span>
                        </a>
                    </li>
                    <li class="m-nav__separator">
                        -
                    </li>
                    <li class="m-nav__item">
                        <a href="#" class="m-nav__link">
                            <span class="m-nav__link-text">
                                Attendance List
                            </span>
                        </a>
                    </li>
                </ul>
                <div style="    float: right;padding: 5px 0px 0px 0px;">
                    <input style="    width: 277px; border: none;border-radius: 72px;padding: 5px 7px 5px 7px;" type="search" name="search" placeholder="Search">
                </div>
            </div>
            
        </div>
    </div>

    <div class="m-content">
    	<div class="m-portlet">
			<div class="m-portlet__head">
				<div class="m-portlet__head-caption">
					<div class="m-portlet__head-title">
						<!-- <span class="m-portlet__head-icon">
							<i class="flaticon-users"></i>
						</span> -->
						<h3 class="m-portlet__head-text">
							Download Attendance List
						</h3>
					</div>
				</div>
			</div>
				<form class="form-horizontal" role="form" method="POST" action="{{url('/hr/attendance_list')}}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="m-portlet__body">
                        @if ($errors->any())
                            <div class="alert alert-danger"> 
                                {{ implode('', $errors->all(':message')) }}
                            </div>
                        @endif
                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>From</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
                                <input class="form-control m-input" type="date" name="from" value="{{ old('from') }}" >
                            </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>To</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
                                <input class="form-control m-input" type="date" name="to" value="{{ old('to') }}" >
                            </div>
                        </div>
                        <!-- <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>Month</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
                                <select name="type" class="form-control m-input">
                                    <option value="current">Current</option>
                                    <option value="previous">Previous</option>
                                </select>
                            </div>
                        </div> -->
					</div>
					<div class="m-portlet__foot">
						<div class="row align-items-center">
							<div class="col-lg-6 m--valign-middle">
								<a href="{{url('hr')}}" class="m-link m--font-bold">
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
@stop
