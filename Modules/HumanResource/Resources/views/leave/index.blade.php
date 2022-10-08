
@extends('layout')

@section('header')

@endsection



@section('content')


    <div class="page-header clearfix">
        <div class="align-items-center">
            <div class="mr-auto" style="display: flex; justify-content: space-between; align-items: center; ">
                <div>
                    <h1 class="m-subheader__title m-subheader__title--separator">
                    Leaves
                    </h1>
                </div>
                
                <!-- <div style="    float: right;padding: 5px 0px 0px 0px;">
                    <input id="myInput" style="width: 277px; border: none;border-radius: 72px;padding: 5px 7px 5px 7px;" type="search" name="search" placeholder="Search">
                </div> -->
            </div>
            
        </div>
    </div>

    <div class="content-panel">
        <div class="m-portlet m-portlet--mobile">
            <div class="m-portlet__head">
                
            </div>
            <div class="m-portlet__body">
                @if(session()->has('message.level'))
                    <div class="alert alert-{{ session('message.level') }}"> 
                    {!! session('message.content') !!}
                    </div>
                @endif
                <!--begin: Search Form -->
                <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                    <div class="row align-items-center">
                        <div class="col-lg-12 order-2 order-xl-1">
                            <div class="form-group m-form__group row align-items-center">
                                <div class="col-md-2">
                                    <a href="{{ url('/hr/leave') }}" class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--pill"
                                     style="    margin-left: 10px;">
                                        <span>
                                            <i class="flaticon-danger"></i>
                                            <span>
                                                Single Leave 
                                            </span>
                                        </span>
                                    </a>
                                </div>
                                <!-- <div class="col-md-6">
                                    {{-- <div class="m-input-icon m-input-icon--left">
                                        <input type="text" class="form-control m-input" placeholder="Search..." id="m_form_search">
                                        <span class="m-input-icon__icon m-input-icon__icon--left">
                                            <span>
                                                <i class="la la-search"></i>
                                            </span>
                                        </span>
                                    </div> --}}
                                </div> -->
                                <div class="col-md-6">    
                            @if (is_admin()  || Auth::user()->can('add-leave') )
                            <a href="{{ url('/hr/leave/mul') }}" class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--pill">
                            {{-- @if (is_allowed('access-hr'))
                            <a href="{{ url('/hr/leave') }}" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--pill"> --}}

                                <span>
                                    <i class="flaticon-danger"></i>
                                    <span>
                                      Multiple Leaves
                                    </span>
                                </span>
                            </a>
                            @endif
                            <div class="m-separator m-separator--dashed d-xl-none"></div>
                        </div>
                        </div>
                        </div>
                        <!-- <div class="col-xl-4 order-1 order-xl-2 m--align-right"> -->
                        <!-- <div class="col-md-6">    
                            @if (is_admin()  || Auth::user()->can('add-leave') )
                            <a href="{{ url('/hr/leave/mul') }}" class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--pill">
                            {{-- @if (is_allowed('access-hr'))
                            <a href="{{ url('/hr/leave') }}" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--pill"> --}}

                                <span>
                                    <i class="flaticon-danger"></i>
                                    <span>
                                      Multiple Leaves
                                    </span>
                                </span>
                            </a>
                            @endif
                            <div class="m-separator m-separator--dashed d-xl-none"></div>
                        </div> -->
                    </div>
                </div>
                <!--end: Search Form -->

                
                <!--begin: Datatable -->
                <div class="row" >
                <div class="col-sm-6"><div class="dataTables_length" id="DataTables_Table_0_length"><label>Show <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-control input-sm"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select> entries</label></div></div>
                <div class="col-sm-6" style="text-align: right; ">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <label>Search:
                            <input type="search" class="form-control input-sm" 
                                placeholder="" aria-controls="DataTables_Table_0">
                        </label>
                    </div>
                </div>
            </div>
                <div class="table-responsive">
                    <!-- <table  class="m-datatable__table datatable"> -->
                        <table class="table table-condensed table-striped ">
                        <thead class="m-datatable__head">
                            <tr class="m-datatable__row">
                            <!-- <th data-field="RecordID" class="m-datatable__cell--center m-datatable__cell m-datatable__cell--check m-datatable__cell--sort">
                            <span>
                            <label class="m-checkbox m-checkbox--single m-checkbox--all m-checkbox--solid m-checkbox--brand">
                            <input type="checkbox"><span></span></label></span></th> -->
                            <th data-field="ID" class="m-datatable__cell m-datatable__cell--sort"><span>Employee ID</span></th>
                            <th data-field="Name" class="m-datatable__cell m-datatable__cell--sort"><span>Employee Name</span></th>
                            <th data-field="Name" class="m-datatable__cell m-datatable__cell--sort"><span>Employee Type</span></th>
                            <th data-field="Date" class="m-datatable__cell m-datatable__cell--sort"><span>Day</span></th>
                            <th data-field="Type" class="m-datatable__cell m-datatable__cell--sort"><span>Type</span></th>
                            <th data-field="Action" class="m-datatable__cell m-datatable__cell--sort"><span>Action</span></th>

                            </tr>
                        </thead>
                        <tbody >
                        <tr class="odd"><td valign="top" colspan="8" class="dataTables_empty">No data available in table</td></tr>
                        </tbody>
                    </table>
                    <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">Showing 0 to 0 of 0 entries</div>
                </div>
                <!--end: Datatable -->
                <div class="dataTables_paginate paging_simple_numbers" 
                id="DataTables_Table_0_paginate" style="text-align: right;">
                <ul class="pagination">
                    <li class="paginate_button previous disabled" 
                        id="DataTables_Table_0_previous">
                        <a href="#" aria-controls="DataTables_Table_0" 
                            data-dt-idx="0" tabindex="0">
                            Previous
                        </a>
                    </li>
                    <li class="paginate_button next disabled" 
                        id="DataTables_Table_0_next">
                        <a href="#" aria-controls="DataTables_Table_0" 
                            data-dt-idx="1" tabindex="0">
                            Next
                        </a>
                    </li>
                </ul>
                </div>
            </div>
        </div>
    </div>
    @stop


@section('scripts')
<script src="{{asset('/assets/js/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript">
    var url = "<?php echo url('hr')?>";
      
    /* get data from database*/
    $(document).ready(function() {
        $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("hr/leaves_datatable") }}',
            columns: [

                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'employee_type', name: 'employee_type' },
                { data: 'day', name: 'day' },
                { data: 'type', name: 'type' },
                { data: 'action', name: 'action'}                
            ]
        });
        // $('#DataTables_Table_0_filter').remove();
        // $('#DataTables_Table_0_length').remove();

        // $('#DataTables_Table_0_info').remove();
        // $('#DataTables_Table_0_paginate').remove();
    });

  /* Search Data from table*/  
    $(document).ready(function() {
        var table = $('.datatable').DataTable();
         
        // Event listener to the two range filtering inputs to redraw on input
        $('#m_form_search').keyup( function() {
            table.search($(this).val()).draw();
        } );
    }); 

    $("body").on("click",".remove-item",function(){
        if (confirm("Are you sure you want to delete this!")) {

            var id = $(this).data('id');
            $.ajax({
                dataType: 'json',
                type:'DELETE',
                url: url + '/delete_leave/' + id,
            }).done(function(){
                
                toastr.success('Deleted Successfully.', 'Success Alert', {timeOut: 5000});
                window.setTimeout(function(){location.reload()}, 1000);
            }).fail(function(request){
                    $.each(request.responseJSON, function(d,t){
                    toastr.error(t.join(""),'Not Deleted .', 'Failed Alert', {timeOut: 5000});
                });

            }); 
        }
    }); 
</script>

@endsection