@extends('reporting')
@section('header')
<title> Cash In Hand Report </title>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3> Cash In Hand Report </h3>
	<h4>Report {{ date_format_app($to) }}</h4>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
  <br>
</div>
@endsection
@section('content')
<div class="row showback">
	<form method="GET" action="{{url('reports')}}/cash_in_hand">
        <div class="col-md-12">
          <div class="form-group">
            <label>To Date</label>
            <input type="date" class="form-control" name="to" value="{{($to)?:date('Y-m-t')}}">
          </div>
        </div>
			<center>
        <div class="form-group">
            <a href="{{ url('admin_transactions') }}" class="btn btn-success">Admin Transactions</a>
            <button type="submit" class="btn btn-success">Search Data</button>
        </div>
      </center>
		</form>
        <div class="col-md-12">
            <h3> Admin Transaction Details </h3>
            <table class="table datatable">
              <thead>
                <th> SR# </th>
                <th> Date </th>
                <th> Type </th>
                <th> Debit [In] </th>
                <th> Credit [Out] </th>
                <th> Mode </th>
                <th> Bank </th>
                <th> Bank Branch </th>
                <th> Description </th>
              </thead>
              <tbody>

               <?php $count = 1;?>
                @foreach($admin_transactions as $trans)
                <?php
                  $in_amount = '';
                  $out_amount = '';
                  if($trans->type=='in')
                  { $in_amount = $trans->amount;  }
                  if($trans->type=='out')
                  { $out_amount = $trans->amount; }
                ?>
                <tr>
                  <td>{{ $count }}</td>
                  <td>{{ date_format_app($trans->date) }}</td>
                  <td> {{($trans->type=="out")?"credit":(($trans->type=="in")?"debit":"")}}  </td>
                  <td>{{ number_format($in_amount)}}</td>
                  <td>{{ number_format($out_amount)}} </td>
                  <td>{{$trans->payment_type}}</td>
                  <td>{{$trans->bank_name}}</td>
                  <td>{{$trans->bank_branch}}</td>
                  <td> {{$trans->description}}  </td>
                </tr>
               <?php $count++; ?>
                @endforeach
              </tbody>
            </table>
                </div>
	<div class="col-md-12">
<h3> Cash In Hand Details </h3>
<center id="ctfooter">
<!-- For Previous Invoices -->
@foreach($previous_invoices as $pre_invo)
    <?php
      $type = $pre_invo->type;
      $o_type = $pre_invo->order_type;
      $total = $pre_invo->order_total;
      $p_type = $pre_invo->payment_type;
      
      if($type == 'in' && ((($o_type=='sale_order' || $o_type=='sale') && $total>'0') || ($o_type=='purchase' && $total<'0') || ($o_type=='')))
      {
        $previous_in_total += $pre_invo->amount;
        switch ($p_type) {
          case "cash":
            $previous_cash_in_total += $pre_invo->amount;
            break;
          case "cheque":
            $previous_cheque_in_total += $pre_invo->amount;
            break;
          case "transfer":
            $previous_transfer_in_total += $pre_invo->amount;
            break;
        }
      }

      if(($type == 'out' || $type=='expense') && (($o_type=='purchase' && $total>'0') || ($o_type=='sale' && $total<'0') || ($o_type=='')))
      {
        $previous_out_total += $pre_invo->amount;
        switch ($p_type) {
          case "cash":
            $previous_cash_out_total += $pre_invo->amount;
            break;
          case "cheque":
            $previous_cheque_out_total += $pre_invo->amount;
            break;
          case "transfer":
            $previous_transfer_out_total += $pre_invo->amount;
            break;
        }
      }
      // ($type == 'in' && ((($o_type=='sale_order' || $o_type=='sale') && $total>'0') || ($o_type=='purchase' && $total<'0') || ($o_type==''))) ? ($previous_in_total += $pre_invo->amount) : '';
      
      // (($type == 'out' || $type=='expense') && (($o_type=='purchase' && $total>'0') || ($o_type=='sale' && $total<'0') || ($o_type==''))) ? ($previous_out_total += $pre_invo->amount) : '';

      
    ?>
@endforeach

@foreach($previous_admin_transactions as $pre_trans)
    <?php
        if($pre_trans->type == 'in')
        {
            $previous_admin_in_total += $pre_trans->amount;
            switch ($pre_trans->payment_type) {
              case "cash":
                $previous_admin_cash_in_total += $pre_trans->amount;
                break;
              case "cheque":
                $previous_admin_cheque_in_total += $pre_trans->amount;
                break;
              case "transfer":
                $previous_admin_transfer_in_total += $pre_trans->amount;
                break;
            }
        }

        if($pre_trans->type == 'out')
        {
            $previous_admin_out_total += $pre_trans->amount;
            switch ($pre_trans->payment_type) {
              case "cash":
                $previous_admin_cash_out_total += $pre_trans->amount;
                break;
              case "cheque":
                $previous_admin_cheque_out_total += $pre_trans->amount;
                break;
              case "transfer":
                $previous_admin_transfer_out_total += $pre_trans->amount;
                break;
            }
        }
      // ($pre_trans->type == 'in') ? ($previous_admin_in_total += $pre_trans->amount) : '';

      // ($pre_trans->type == 'out') ? ($previous_admin_out_total += $pre_trans->amount) : '';
    ?>
@endforeach
<?php 
    $previous_profitloss_calculation = ($previous_in_total+$previous_admin_in_total)-($previous_out_total+$previous_admin_out_total);

    $previous_profitloss_cash = ($previous_cash_in_total+$previous_admin_cash_in_total)-($previous_cash_out_total+$previous_admin_cash_out_total);

    $previous_profitloss_cheque = ($previous_cheque_in_total+$previous_admin_cheque_in_total)-($previous_cheque_out_total+$previous_admin_cheque_out_total);

    $previous_profitloss_transfer = ($previous_transfer_in_total+$previous_admin_transfer_in_total)-($previous_transfer_out_total+$previous_admin_transfer_out_total);
?>

<!-- For Today Invoices -->
@foreach($today_invoices as $today_invo)
    <?php
      $type = $today_invo->type;
      $o_type = $today_invo->order_type;
      $total = $today_invo->order_total;
      $p_type = $today_invo->payment_type;

      if($type == 'in' && ((($o_type=='sale_order' || $o_type=='sale') && $total>'0') || ($o_type=='purchase' && $total<'0') || ($o_type=='')))
      {
        $today_in_total += $today_invo->amount;
        switch ($p_type) {
          case "cash":
            $today_cash_in_total += $today_invo->amount;
            break;
          case "cheque":
            $today_cheque_in_total += $today_invo->amount;
            break;
          case "transfer":
            $today_transfer_in_total += $today_invo->amount;
            break;
        }
      }

      if(($type == 'out' || $type=='expense') && (($o_type=='purchase' && $total>'0') || ($o_type=='sale' && $total<'0') || ($o_type=='')))
      {
        $today_out_total += $today_invo->amount;
        switch ($p_type) {
          case "cash":
            $today_cash_out_total += $today_invo->amount;
            break;
          case "cheque":
            $today_cheque_out_total += $today_invo->amount;
            break;
          case "transfer":
            $today_transfer_out_total += $today_invo->amount;
            break;
        }
      }
      
      // ($type == 'in' && ((($o_type=='sale_order' || $o_type=='sale') && $total>'0') || ($o_type=='purchase' && $total<'0') || ($o_type==''))) ? ($today_in_total += $today_invo->amount) : '';
      
      // (($type == 'out' || $type=='expense') && (($o_type=='purchase' && $total>'0') || ($o_type=='sale' && $total<'0') || ($o_type==''))) ? ($today_out_total += $today_invo->amount) : '';
    ?>
@endforeach

@foreach($today_admin_transactions as $today_trans)
    <?php
        if($today_trans->type == 'in')
        {
            $today_admin_in_total += $today_trans->amount;
            switch ($today_trans->payment_type) {
              case "cash":
                $today_admin_cash_in_total += $today_trans->amount;
                break;
              case "cheque":
                $today_admin_cheque_in_total += $today_trans->amount;
                break;
              case "transfer":
                $today_admin_transfer_in_total += $today_trans->amount;
                break;
            }
        }

        if($today_trans->type == 'out')
        {
            $today_admin_out_total += $today_trans->amount;
            switch ($today_trans->payment_type) {
              case "cash":
                $today_admin_cash_out_total += $today_trans->amount;
                break;
              case "cheque":
                $today_admin_cheque_out_total += $today_trans->amount;
                break;
              case "transfer":
                $today_admin_transfer_out_total += $today_trans->amount;
                break;
            }
        }
      // ($today_trans->type == 'in') ? ($today_admin_in_total += $today_trans->amount) : '';

      // ($today_trans->type == 'out') ? ($today_admin_out_total += $today_trans->amount) : '';
    ?>
@endforeach
<?php
    $today_profitloss_calculation = ($today_in_total+$today_admin_in_total)-($today_out_total+$today_admin_out_total);

    $today_profitloss_cash = ($today_cash_in_total+$today_admin_cash_in_total)-($today_cash_out_total+$today_admin_cash_out_total);

    $today_profitloss_cheque = ($today_cheque_in_total+$today_admin_cheque_in_total)-($today_cheque_out_total+$today_admin_cheque_out_total);

    $today_profitloss_transfer = ($today_transfer_in_total+$today_admin_transfer_in_total)-($today_transfer_out_total+$today_admin_transfer_out_total); 
?>
    <br>
    <table border="1" width="300px" style="font-size: 18px">
        <thead>
            <tr>
                <th colspan="2">Opening Balance Summary</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: left">Previous Debit[In]</td>
                <td style="text-align: right">{{number_format(abs($previous_in_total))}}</td>
            </tr>
            <tr>
                <td style="text-align: left">Previous Credit[Out]</td>
                <td style="text-align: right">{{number_format(abs($previous_out_total))}}</td>
            </tr>
            <tr>
                <td style="text-align: left">Previous Admin Debit[In]</td>
                <td style="text-align: right">{{number_format(abs($previous_admin_in_total))}}</td>
            </tr>
            <tr>
                <td style="text-align: left">Previous Admin Credit[Out]</td>
                <td style="text-align: right">{{number_format(abs($previous_admin_out_total))}}</td>
            </tr>
            <tr>
                <td style="text-align: left">Opening Balance</td>
                <td class="{{($previous_profitloss_calculation<0)?'red':'green'}}" style="text-align: right"> {{ amount_cdr($previous_profitloss_calculation,true) }} </td>
            </tr>
        </tbody>
    </table>
    <table border="1" width="300px" style="font-size: 18px">
        <thead>
            <tr>
                <th colspan="2">Cash In Hand Summary</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: left">Today Debit[In]</td>
                <td style="text-align: right">{{number_format(abs($today_in_total))}}</td>
            </tr>
            <tr>
                <td style="text-align: left">Today Credit[Out]</td>
                <td style="text-align: right">{{number_format(abs($today_out_total))}}</td>
            </tr>
            <tr>
                <td style="text-align: left">Today Admin Debit[In]</td>
                <td style="text-align: right">{{number_format(abs($today_admin_in_total))}}</td>
            </tr>
            <tr>
                <td style="text-align: left">Today Admin Credit[Out]</td>
                <td style="text-align: right">{{number_format(abs($today_admin_out_total))}}</td>
            </tr>
            <tr>
                <td style="text-align: left">Today Balance</td>
                <td class="{{($today_profitloss_calculation<0)?'red':'green'}}" style="text-align: right"> {{ amount_cdr($today_profitloss_calculation,true) }} </td>
            </tr>
            <?php $cash_balance     = $previous_profitloss_cash + $today_profitloss_cash  ?>
            <?php $cheque_balance   = $previous_profitloss_cheque + $today_profitloss_cheque  ?>
            <?php $transfer_balance = $previous_profitloss_transfer + $today_profitloss_transfer  ?>
            <?php $cash_in_hand     = $previous_profitloss_calculation + $today_profitloss_calculation  ?>
            <tr>
                <td style="text-align: left">Cash Balance</td>
                <td class="{{($cash_balance<0)?'red':'green'}}" style="text-align: right"> {{ amount_cdr($cash_balance,true) }} </td>
            </tr>
            <tr>
                <td style="text-align: left">Cheque Balance</td>
                <td class="{{($cheque_balance<0)?'red':'green'}}" style="text-align: right"> {{ amount_cdr($cheque_balance,true) }} </td>
            </tr>
            <tr>
                <td style="text-align: left">Online Transfer Balance</td>
                <td class="{{($transfer_balance<0)?'red':'green'}}" style="text-align: right"> {{ amount_cdr($transfer_balance,true) }} </td>
            </tr>
            <tr>
                <td style="text-align: left">Cash In Hand</td>
                <td class="{{($cash_in_hand<0)?'red':'green'}}" style="text-align: right"> {{ amount_cdr($cash_in_hand,true) }} </td>
            </tr>
        </tbody>
    </table>
</center>
	</div>
</div>
    <script type="text/javascript" src="{{asset('/assets/js/jquery.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.colVis.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.buttons.js')}}?v=1"></script>
     <script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.flash.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.print.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.html5.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/datatables/jszip.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/datatables/pdfmake.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/datatables/vfs_fonts.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
    const company = "{{session()->get('settings.profile.company')}}";
    const phone = "{{session()->get('settings.profile.phone')}}";
    const address = "{{session()->get('settings.profile.address')}}";
    $(document).ready(function(e) {
      const brandingHtml = "<h1>" + company + "</h1><address><strong>" + address + "<br><abbr>Phone:</abbr>" + phone + "</address>";
      const brandingPdf = company + "\n" + address + "\nPhone: " + phone;
      var messageTopContentName = "";
      var messageTopContentPhone = "";
      var pdfNewLine = "\n";
      var printNewLine = "<br>";
    	var messageTopContent	 = 'Revenue Report date: {{ date_format_app($from) }} to  {{ date_format_app($to) }}';
    	var messageBottomContent = "\nReports by eAsasa ( 0345 4777487)";
      $(".datatable").DataTable({
        "deferRender": true,
        dom: 'Bfrtip',
        "ordering": true, 
        stateSave: true,
        "stateLoadParams": function (settings, data) {
            data.search.search = "";
            data.length = 10;
            data.start = 0;
            data.order = [];
        },           
        buttons: [
          'copy', {
              extend: 'excel',
              title: "Revenue Report",
              messageTop: messageTopContentName+pdfNewLine+messageTopContent,
              messageBottom: messageBottomContent,
              exportOptions: { columns: ':visible' }
            }, {
              extend: 'pdf',
              title: "Revenue Report",
              messageTop: brandingPdf+pdfNewLine+pdfNewLine+messageTopContentName+pdfNewLine+messageTopContentPhone+pdfNewLine+messageTopContent,
              messageBottom: messageBottomContent,
              exportOptions: { columns: ':visible' }
            }, {
              extend: 'print',
              title: "Revenue Report",
              messageTop: brandingHtml+printNewLine+printNewLine+messageTopContentName+printNewLine+messageTopContentPhone+printNewLine+messageTopContent,
              messageBottom: messageBottomContent,
              exportOptions: { columns: ':visible' }
            },
            'colvis'
        ]
    });
  });
</script>
@endsection