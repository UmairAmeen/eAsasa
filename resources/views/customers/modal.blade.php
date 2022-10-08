           <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" data-backdrop="static" id="addcustomermodal" class="modal fade">
        <div class="modal-dialog" style="width: 90%;">
         <div class="modal-content col-md-12 content-panel" style=" min-height: 300px">
          <div class="col-md-12">
<div class="row">
        <div class="col-md-10">
        <form action="{{ route('customers.store') }}" method="POST" id="customer_modal_form">
            <div id="log" class=""></div>
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" class="form-control" placeholder="Customer Name">
                 <small id="emailHelp" class="form-text text-muted">This is required</small>
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="tel" name="phone" class="form-control" placeholder="Customer Mobile Number">
              <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
            </div>

             <div class="form-group">
                <label>City:</label>
                <input type="text" name="city" class="form-control" placeholder="Customer City Name">
              <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
            </div>


            <div class="form-group">
              <label>Address:</label>
              <input type="text" name="address" class="form-control" placeholder="Customer Address Name">
          </div>

            <div class="form-group">
                <label for="type">Type:</label>
                   <select id="type" name="type" class="form-control">
                       <option value="tour">Tour</option>
                       <option value="counter">Counter</option>
                   </select>

            </div>

            <div class="form-group">
              <label>Opening Balance</label>
              <input class="form-control" placeholder="Opening Balance" name="openingbalance">
              <small id="emailHelp" class="form-text text-muted">This is credit amount</small>
            </div>


            <div class="form-group">
              <label>Manual Registeration Number</label>
              <input type="text" class="form-control" name="registeration_number" placeholder="Registeration Number">
            </div>

             <div class="form-group">
              <label>Last Call</label>
              <input class="form-control" type="date" placeholder="Last Call" name="last_contact_on">
              <small id="emailHelp" class="form-text text-muted">When you last reminded for payment or last paid, notification will base on this</small>
            </div>


            <div class="form-group">
              <label>Enable Payment Reminder</label>
              <input type="checkbox" name="payment_remainder" checked="checked" data-toggle="switch">
            </div>

            <div class="form-group">
              <label>Payment Reminder Days</label>
              <input type="number" min="1" class="form-control" name="remainder_days" value="7">
              <small id="emailHelp" class="form-text text-muted">After How Many Days, did it remaind for Payment</small>
            </div>


                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <input type="hidden" name="modal_redirection" value="true">
                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <button type="button" class="btn btn-link pull-right" data-dismiss="modal"><i class="glyphicon glyphicon-backward"></i> Cancel</button>
                </div>
            </form>

        </div>
    </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/js/bootstrap-datepicker.min.js"></script>
  <!--custom switch-->
  <script src="{{asset('assets/js/bootstrap-switch.js')}}"></script>

  <script>
    $('.date-picker').datepicker({
    });
$(document).ready(function(){
    $("[data-toggle='switch']").wrap('<div class="switch" />').parent().bootstrapSwitch();
  
});
  </script>
</div>
</div>
</div>
</div>