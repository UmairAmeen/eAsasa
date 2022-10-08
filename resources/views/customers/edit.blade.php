@extends('layout')
@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-edit"></i> Customers / Edit #{{$customer->id}}</h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-12">

            <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                 <div id="log" class=""></div>
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="{{$customer->name}}" class="form-control" placeholder="Customer Name">
                 <small id="emailHelp" class="form-text text-muted">This is required</small>
            </div>
            <div class="form-group">
              <label>CNIC:</label>
              <input type="text" name="cnic" value="{{$customer->cnic}}" class="form-control" placeholder="Customer CNIC">
               {{-- <small id="emailHelp" class="form-text text-muted">This is required</small> --}}
          </div>
          <div class="form-group">
            <label>NTN:</label>
            <input type="text" name="ntn" value="{{$customer->ntn}}" class="form-control" placeholder="Customer NTN">
             {{-- <small id="emailHelp" class="form-text text-muted">This is required</small> --}}
        </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="tel" name="phone" value="{{$customer->phone}}" class="form-control" placeholder="Customer Mobile Number">
                <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
            </div>

            <div class="form-group">
                <label>City:</label>
                <input type="text" name="city" value="{{$customer->city}}" class="form-control" placeholder="Customer City">
                <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
            </div>
           
            <div class="form-group">
              <label>Address:</label>
              <input type="text" name="address" value="{{$customer->address}}" class="form-control" placeholder="Customer Address">
              <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
          </div>

            <div class="form-group">
                <label>Type:</label>
                <select name="type" class="form-control">
                    <option value="tour" {{($customer->type=="tour")?'selected':''}}>Tour</option>
                    <option value="tour" {{($customer->type=="counter")?'selected':''}}>Counter</option>
                </select>

            </div>
             <div class="form-group">
              <label>Manual Registeration Number</label>
              <input type="text" class="form-control" name="registeration_number" placeholder="Registeration Number" value="{{$customer->registeration_number}}">
            </div>
            <div class="form-group">
              <label>Last Call</label>
              <input class="form-control" type="date" placeholder="Last Call" name="last_contact_on" value="{{$customer->last_contact_on}}">
              <small id="emailHelp" class="form-text text-muted">When you last reminded for payment or last paid</small>
            </div>

            <div class="form-group">
              <label>Enable Payment Reminder</label>
              <input type="checkbox" name="payment_remainder" {{($customer->payment_notify)?'checked="checked"':""}} data-toggle="switch">
            </div>

            <div class="form-group">
              <label>Payment Reminder Days</label>
              <input type="number" min="0" class="form-control" name="remainder_days" value="{{$customer->after_last_payment}}">
              <small id="emailHelp" class="form-text text-muted">After How Many Days, did it remaind for Payment</small>
            </div>
            <div class="form-group">
              <label>Notes</label>
              <textarea class="form-control" name="notes">{{$customer->notes}}</textarea>
            </div>

                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a class="btn btn-link pull-right" href="{{ route('customers.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>
                </div>
            </form>

        </div>
    </div>
@endsection
@section('scripts')
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
@endsection
