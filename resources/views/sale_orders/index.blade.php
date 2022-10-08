@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="fas fa-truck-loading"></i> SaleOrders
             @if (is_allowed('sale-create'))
            <a accesskey="a" class="btn btn-success pull-right" href="{{ url('pos') }}"><i class="glyphicon glyphicon-plus"></i> POS <br><small>ALT + A</small></a>
            <a accesskey="n" class="btn btn-success pull-right" href="{{ route('sale_orders.create') }}"><i class="glyphicon glyphicon-plus"></i> Create <br><small>ALT + N</small></a>
            @endif
        </h1>
        <small><span class="btn btn-info btn-sm">Update:</span> <marquee>Click on <b>POSTED: NO</b> button to confirm the order, without opening the order</marquee>    </small>

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 content-panel">
            <div id="gridContainer"></div>
                <table class="table table-condensed table-striped sale_order_listing">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Bill #</th>
                            <th>Status</th>
                            <th>Customer Name</th>
                            <th>Order #</th>
                            <th>Amount</th>
                            <th>Posted</th>
                            <th>Added By</th>
                            <th>Updated By</th>
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                       
                    </tbody>
                </table>
                

        </div>
    </div>
    <form action="{{url('confirmOrder')}}" method="POST" id="confirmOrder">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="dt" value="1">
    </form>

@endsection
@section('scripts')
<script type="text/javascript">
    var table = null;
    $(document).ready(function() {
        $('.sale_order_listing').hide();
//    table = $('.sale_order_listing').DataTable( {
//   "ajax": "/sale_order_listing_datatable",
//   "processing": true,
//   responsive: true,
//   "serverSide": true,
//   "order": [[0,'desc']],
//    "columns": [
//             { "data": "id", "orderable": true, "searchable": true},
//             {"data":'date'},
//             {"data":"invoice.bill_number","defaultContent":"-"},
//             {"data":"status"},
//             { "data": "customer.name", "defaultContent":"-" },
//             { "data": "invoice_id" },
//             { "data": "total", "searchable": false },
//             { "data": "posted", "searchable": false  },
//             { "data": "added_by", defaultContent:"-", "orderable": false, "searchable": false },
//             { "data": "updated_by", defaultContent:"-", "orderable": false, "searchable": false },
//             { "data": "actions", "orderable": false, "searchable": false, 'exportable': false, 'printable':false }

//         ],
//         "fnDrawCallback": function (oSettings) {
//           init();
//         }
//   });
});
   function changeStatus(id)
   {
    if (!confirm('Are you sure to confirm this Sale Order?'))
    {
        return false;
    }
    $("#confirmOrder input[name=id]").val(id);
    $("#confirmOrder").submit();
    $("#confirmOrder input[name=id]").val(0);
   }

var instance = null;

   $(() => {
  function isNotEmpty(value) {
    return value !== undefined && value !== null && value !== '';
  }
//   Globalize.culture().numberFormat.currency.symbol = "Rs.";

  const store = new DevExpress.data.CustomStore({
    key: 'id',
    load(loadOptions) {
      const deferred = $.Deferred();
      const args = {};

      [
        'skip',
        'take',
        'requireTotalCount',
        'requireGroupCount',
        'sort',
        'filter',
        'totalSummary',
        'group',
        'groupSummary',
      ].forEach((i) => {
        if (i in loadOptions && isNotEmpty(loadOptions[i])) {
          args[i] = JSON.stringify(loadOptions[i]);
        }
      });
      $.ajax({
        url: '{{url('sale_order_json')}}',
        dataType: 'json',
        crossDomain: true,
        data: args,
        success(result) {
          deferred.resolve(result.data, {
            totalCount: result.totalCount,
            summary: result.summary,
            groupCount: result.groupCount,
          });
        },
        error(errr) {
          console.log(errr);
          deferred.reject('Data Loading Error');
        }
      });

      return deferred.promise();
    },
  });

  instance = $('#gridContainer').dxDataGrid({
    dataSource: store,
    allowColumnReordering: true,
    allowColumnResizing: true,
    columnAutoWidth: true,
    showBorders: true,
    showColumnLines:true,
    showRowLines: true,
    rowAlternationEnabled: true,
    showClearButton: true,
    columnChooser: {
      enabled: true,
    },
    columnFixing: {
      enabled: true,
    },
    stateStoring: {  
      enabled: true,  
      storageKey: "storage",  
      type: "custom",  
      customLoad: function () {  
          var state = localStorage.getItem(this.storageKey);
          if (state) {  
              state = JSON.parse(state);
              if (state.columns)
              {
                for (var i = 0; i < state.columns.length; i++) {  
                    state.columns[i].filterValue = null; 
                    state.columns[i].filterValues = null; 
                    state.columns[i].selectedFilterOperation = null; 
                }  
                state.columns[0].sortOrder = 'desc';
              }
          }
          return state;  
      },  
      customSave: function (state) {  
          localStorage.setItem(this.storageKey, JSON.stringify(state));  
      },  
    },
    remoteOperations: true,
    paging: {
      pageSize: 10,
    },
    pager: {
      visible: true,
      allowedPageSizes: [10, 20, 50, 100],
      showPageSizeSelector: true,
      showInfo: true,
      showNavigationButtons: true,
    },
    export: {
            enabled: true,
            fileName: "Sale Order Summary",
            title: 'Export to excel'
    },
    filterRow: {
      visible: true,
      applyFilter: 'auto',
    },
    searchPanel: {
      visible: true,
      width: 240,
      placeholder: 'Search...',
    },
    headerFilter: {
      visible: true,
    },
    columns: [ {
      dataField: 'sale_orders.id',
      calculateCellValue: function(rowData) {
        return rowData.id;
        },
      dataType: 'number',
      allowFiltering:true,
      sortOrder: "desc",
        width: 100,
    },
    {
      dataField: 'bill_number',
      dataType: 'bill_number',
      caption:"Bill #",
      format:'dd-MM-yyyy',
      showClearButton: true,
    },
     {
      dataField: 'date',
      dataType: 'date',
      caption:"Date",
      format:'dd-MM-yyyy',
      showClearButton: true,
    },{
      caption: "Status",
      dataField: 'status',
      dataType: 'number',
      cellTemplate(container, options) {
        $('<div>')
          .append(options.value)
          .appendTo(container);
      }
    },
    {
        caption: "Customer",
        columns:[
        {
          dataField: 'name',
            caption:"Name",
            dataType: 'string',
        }
        ,{
          dataField: 'city',
            caption:"City",
            dataType: 'string',
        }]
    },
    {
     //name of the field should be order#
      caption:"Order #",
      dataField: 'invoice_id',
      dataType: 'number',
      width:100,
    }
    
    ,{
      dataField: 'posted',
      dataType: 'boolean',
      falseText:"Not Posted",
      width:100,
      cellTemplate(container, options) {
        $('<div>')
          .append(options.value)
          .appendTo(container);
      },
    },{
      dataField: 'invoice.total',
      calculateCellValue: function(rowData) {
                return thousands_separators(parseFloat(rowData.total).toFixed(2));
        },
      dataType: 'number',
    //   allowFiltering:false,
      format: {
                type: "fixedPoint",
                precision: 2
            }
    },{
      dataField: 'balance',
      dataType: 'number',
      format: {
                type: "fixedPoint",
                precision: 2
            }
    },
    {
      caption: "Sales Person",
      dataField: 'sales_person',
      dataType: 'string',
      allowSorting: false,
      allowFiltering:false,
      allowExporting:false,
      width:60
    },
    {
      dataField: 'added_by',
      dataType: 'string',
      allowSorting: false,
      allowFiltering:false,
      allowExporting:false,
      width:60
    }
    ,{
      dataField: 'updated_by',
      dataType: 'string',
      allowSorting: false,
      allowFiltering:false,
      allowExporting:false,
      width:60
    },
{
    dataField: 'action',
      dataType: 'object',
      allowSorting: false,
      allowFiltering:false,
      cellTemplate(container, options) {
        $('<div>')
          .append(options.value)
          .appendTo(container);
      },
      allowExporting:false
}],
  }).dxDataGrid('instance');
});

</script>
@endsection