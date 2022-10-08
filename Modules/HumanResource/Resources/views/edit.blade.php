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
                                Edit Employee
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
                            Edit Employee
                        </h3>
                    </div>
                </div>
            </div>
                <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{url('/hr/update')}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="{{ $employee->id }}">
		            
                    <div class="m-portlet__body">
                        @if ($errors->any())
                                {{ implode('', $errors->all(':message')) }}
                        @endif

                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>Name</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
                        		<input type="text" name="name" value="{{ $employee->name }}" class="form-control m-input" required>
                            </div>
                        </div>                        
                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>Father Name</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
								<input type="text" class="form-control m-input" name="father_name" value="{{ $employee->father_name }}" required>
		                    </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>CNIC</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
								<input class="form-control m-input" type="text" name="cnic" value="{{ $employee->cnic }}" required minlength="13" maxlength="15">
		                    </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>Address</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
								<input type="text" class="form-control m-input" name="address" value="{{ $employee->address }}" required>
		                    </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>Phone</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
								<input type="text" class="form-control m-input" name="phone" value="{{ $employee->phone }}" required minlength="7" maxlength="15">
		                    </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>Position</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
								<input type="text" class="form-control m-input" name="position" value="{{ $employee->position }}" required>
		                    </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>Type</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
                                <select class="form-control m-input" name="type">
                                    <option value="Daily Wage" {{ ($employee->type == 'Daily Wage' ? 'selected' : '') }}>Daily Wage</option>
									<option value="Contract" {{ ($employee->type == 'Contract' ? 'selected' : '') }}>Contract</option>
									<option value="Salary" {{ ($employee->type == 'Salary' ? 'selected' : '') }}>Salary</option>
                                </select>
		                    </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>Salary</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
								<input type="text" class="form-control m-input" name="salary" value="{{ $employee->salary }}" required>
                            </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>Description</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
                                <input type="text" class="form-control m-input" name="description" value="{{ $employee->description }}" required>
                            </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>Date of Joining</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
								<input type="date" class="form-control m-input" name="date_of_joining" value="{{ $employee->date_of_joining }}" required>
		                    </div>
                        </div>
                        <div class="form-group m-form__group row">
							<label class="col-lg-2 col-form-label">
								<strong>Picture</strong>
							</label>
							<div class="col-lg-6 col-form-label">
								<label class="custom-file">
									<input type="file" name="picture" id="picture" class="custom-file-input">
									<span class="custom-file-control"></span>
								</label>
							</div>
						</div>
						<div class="form-group m-form__group row">
							<label class="col-lg-2 col-form-label">
								<strong>CNIC Front</strong>
							</label>
							<div class="col-lg-6 col-form-label">
								<label class="custom-file">
									<input type="file" name="cnic_front" id="cnic_front" class="custom-file-input" >
									<span class="custom-file-control"></span>
								</label>
							</div>
						</div>
						<div class="form-group m-form__group row">
							<label class="col-lg-2 col-form-label">
								<strong>CNIC Back</strong>
							</label>
							<div class="col-lg-6 col-form-label">
								<label class="custom-file">
									<input type="file" name="cnic_back" id="cnic_back" class="custom-file-input" >
									<span class="custom-file-control"></span>
								</label>
							</div>
						</div>
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