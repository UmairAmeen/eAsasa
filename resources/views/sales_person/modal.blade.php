<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" data-backdrop="static" id="addSalesPersonmodal"
    class="modal fade">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content col-md-12 content-panel" style=" min-height: 300px">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-10">
                        <form action="{{ route('salesPerson.store') }}" method="POST" id="salesPerson_modal_form">
                            <div id="log" class=""></div>
                            <div class="form-group">
                                <label>Name:</label>
                                <input type="text" name="name" class="form-control" placeholder="Sales Person Name">
                                <small id="emailHelp" class="form-text text-muted">This is required</small>
                            </div>
                            <div class="form-group">
                                <label>Phone:</label>
                                <input type="tel" name="phone" class="form-control"
                                    placeholder="Sales Person Mobile Number">
                            </div>
                            <div class="form-group">
                                <label>Address:</label>
                                <input type="text" name="address" class="form-control"
                                    placeholder="Sales Person Address">
                            </div>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <input type="hidden" name="modal_redirection" value="true">
                            <div class="well well-sm">
                                <button type="submit" class="btn btn-primary">Create</button>
                                <button type="button" class="btn btn-link pull-right" data-dismiss="modal"><i
                                        class="glyphicon glyphicon-backward"></i> Cancel</button>
                            </div>
                        </form>

                    </div>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/js/bootstrap-datepicker.min.js"></script>
                <!--custom switch-->
                <script src="{{ asset('assets/js/bootstrap-switch.js') }}"></script>

                <script>
                    $('.date-picker').datepicker({});
                    $(document).ready(function() {
                        $("[data-toggle='switch']").wrap('<div class="switch" />').parent().bootstrapSwitch();

                    });
                </script>
            </div>
        </div>
    </div>
</div>
