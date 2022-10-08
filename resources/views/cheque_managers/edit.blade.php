<div class="row">
    <div class="col-md-12">
        <div class="pull-right"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div id="log"></div>
    </div>
    <div class="col-md-12">
        <h1>Edit Cheque #{{$cheque_manager->id}}</h1>
        <h4>Customer: {{($cheque_manager->customer)?$cheque_manager->customer->name:"-"}}</h4>
        <h4>Type: {{($cheque_manager->type=="in")?"Received":"Forwarded"}}</h4>
        <div class="content-panel">
        <form action="{{ route('cheque_managers.update', $cheque_manager->id) }}" method="POST">
            
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">



  <div class="form-group">
    <label for="date">Date</label>
        <input type="date" class="form-control" name="date" value="{{$cheque_manager->date}}">
    <small id="emailHelp" class="form-text text-muted">Date when cheque is received</small>
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Bank</label>
    <input type="text" class="form-control" name="bank" placeholder="Bank" value="{{$cheque_manager->bank}}">
  </div>

   <div class="form-group">
    <label for="exampleInputPassword1">Cheque No</label>
    <input type="text" class="form-control" name="transacion_id" placeholder="Cheque No." value="{{$cheque_manager->transaction_id}}">
  </div>

  <div class="form-group">
    <label for="exampleInputPassword1">Amount</label>
    <input type="text" class="form-control" name="amount" placeholder="Amount" value="{{$cheque_manager->amount}}">
  </div>

  <div class="form-group">
    <label for="date">Release Date</label>
        <input type="date" class="form-control" name="release_date" value="{{$cheque_manager->release_date}}">
    <small id="emailHelp" class="form-text text-muted">Date when cheque will be released</small>
  </div>
  

  <button type="submit" class="btn btn-primary">Submit</button>
  <button data-dismiss="modal" aria-label="Close" class="btn btn-danger">Close</button>
</form>
</div>
    </div>
</div>