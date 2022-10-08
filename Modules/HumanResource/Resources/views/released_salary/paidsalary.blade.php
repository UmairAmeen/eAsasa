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
                                Released Salary
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
							Salary Released
						</h3>
					</div>
				</div>
			</div>
            <center style="padding-top: 15px;">
            <form id="month_change">
                       <input value="{{date('Y-m', strtotime($start_month))}}" type="month" name="month_year" onchange="$('#month_change').submit()">

                       </form><small>
                {{-- {{date_to_str($start_month)}} to {{date_to_str($end_month)}} --}}
            </small>
                
            </center>
			<div class="m-portlet__body">
				<table id="example" class="dataTables_wrapper" style="width:100%;text-align: center">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
							<th>Employee Name</th>
                            <th>Total Salary</th>
                            <th>Released Pay</th>
                            <th>Remaining</td>
                            <th>Advance</th>
                            <th>Month of salary</th>
						</tr>
					</thead>

						<tbody>
                       @foreach ($paidreleasemonthly as $pr)
							<tr>
                                <td class="details-control"></td>
                                <td>{{$pr->id}}</td>
                                <td>{{$pr->employee_id}}</td>
                                <td>{{$pr->Total_Salry}}</td>
                                <td>{{$pr->Total_release}}</td>
                                <td>{{$pr->payment_remaining}}</td>
                                <td>{{$pr->payment_advance}}</td>
                                <td>{{date('Y-M-d',strtotime($pr->date))}}</td>
                                
							</tr>
                        @endforeach
						</tbody>
				</table>
			</div>
				
			
		</div>
    </div>


    <script type="text/javascript">
    $(document).ready(function() {

        var full_details = [
        @foreach ($paidrelease as $pr)
        {"id":{{$pr->id}}, "employee_id":{{$pr->employee_id}}, "date":"{{date('Y-M-d',strtotime($pr->date))}}", "payment":{{$pr->payment}}, "release":{{$pr->payment_release}}, "advance":{{$pr->payment_advance}}, 'remaining':{{$pr->payment_remaining}}, 'userid':{{$pr->user_id}}, "status":"{{$pr->status}}"},
        @endforeach
        ];

        function child_details ( d ) {
            // debugger;
            //we have employee id here d[2]
    // `d` is the original data object for the row
    var rows_html = "";
                        $.each(full_details, function(index, element){
                            var monthdate=new Date(d[7]);
                            var month=monthdate.getMonth();
                            var thisdate=new Date(element.date);
                            var thismonth=thisdate.getMonth();
                            if (element.employee_id == d[2] && month==thismonth)
                            {
                                //if employee_id is same
                                //if month is same
                                rows_html += '<tr>'+
            '<td></td>\
            <td></td>\
            <td>'+element.id+'</td><td>'+element.employee_id+'</td><td>'+element.date+'</td><td>'+element.release+'</td><td>'+element.advance+'</td><td>'+element.remaining+'</td><td>'+element.userid+'</td><td>'+element.status+'</td><td><a href="{{url('/hr/salary')}}/'+element.id+'/edit" data-id="" class="btn btn-outline-success m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill" title="Edit"><i class="la la-edit"></i></a>\
                <a onclick="Removenow(this,'+element.id+')" class="btn btn-outline-danger m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill" title="delete"><i class="la la-trash"></i></a></td></tr>';
                            }
                        });
    return '<table class="dataTables_wrapper" style="width:100%;text-align: center">'+

    '<tr>'+
                            '<th></th>\
                            <th></th>\
                            <th>ID</th>\
                            <th>Employee Name</th>\
                            <th>Date</th>\
                            <th>Released Salary</th>\
                            <th>Advance Pay</th>\
                            <th>Remaining Pay</th>\
                            <th>Released By</th>\
                            <th>Status</th>\
                            </tr><tbody>'+rows_html+'</tbody></table>';
}

        $('#example').DataTable();

    $('#example tbody').on('click', 'td.details-control', function () {

       var table=$('#example').DataTable();
       
        var tr = $(this).closest('tr');
        var row = table.row( tr );
        var f=$(this).closest('tr').hasClass('shown');
        if (f)
        {
        row.child.hide();
        $(this).closest('tr').removeClass('shown');
        }
        else
        {
         row.child( child_details(row.data()) ).show();
         $(this).closest('tr').addClass('shown')  
        }
 
        // if ( row.child.isShown() ) {
        //     // This row is already open - close it
        //     row.child.hide();
        //     tr.removeClass('shown');
        // }
        // else {
        //     // Open this row
        //     row.child( format(row.data()) ).show();
        //     tr.addClass('shown');
        // }
    } );
});
        
        function Removenow(ele,id)
        {
             swal({
  animation: false,
         title: 'Are you sure?',
         text: "You wish to delete paid history",
         type: 'warning',
         showCancelButton: true,
         confirmButtonColor: '#1e8fe1',
         cancelButtonColor: '#ffffff',
         confirmButtonText: 'Delete',
         closeOnConfirm: false,
         showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.value) {
    
            var onsucess=function(data){
                $(ele).parent().parent().remove();
            }
            ajaxcall("{{url('hr/salary')}}/"+id+"/deleted","DELETE",false,false,onsucess);
  }
});
        }

    </script>
@stop