@extends('layout')


      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
@section('content')
	  	
	  		<div id="showtime">License Required!</div>
	  			<div class="col-lg-4 col-lg-offset-4">
	  				<div class="lock-screen">
		  				<h2><a data-toggle="modal" href="#myModal"><i class="fa fa-lock"></i></a></h2>
		  				<p>Enter a new License</p>
		  				
				          <!-- Modal -->
				          <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
				              <div class="modal-dialog">
				                  <div class="modal-content">
                                    <form action="{{route('license.validation')}}" method="POST">
                                      <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                          <h4 class="modal-title">Welcome Back</h4>
                                      </div>
                                      <div class="modal-body">
                                    <div id="log" class=""></div>
                                          <p class="centered"><img class="img-circle" width="80" src="assets/img/ui-sam.jpg"></p>
				                          <input type="text" id="license-field" name="license_key" placeholder="License Key" autocomplete="off" class="form-control placeholder-no-fix">
                                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
				
				                      </div>
				                      <div class="modal-footer centered">
				                          <!-- <button data-dismiss="modal" class="btn btn-theme04" type="button">Cancel</button> -->
				                          <button class="btn btn-theme03" type="submit">Verify License</button>
				                      </div>
                                      </form>
				                  </div>
				              </div>
				          </div>
				          <!-- modal -->
		  				
		  				
	  				</div><! --/lock-screen -->
	  			</div><!-- /col-lg-4 -->
	  	
@endsection
    <!-- js placed at the end of the document so the pages load faster -->
@section('scripts')    

    <!--BACKSTRETCH-->
    <!-- You can use an image of whatever size. This script will stretch to fit in any screen size.-->
    <script type="text/javascript" src="assets/js/jquery.backstretch.min.js"></script>
    <script>
        $.backstretch("assets/img/login-bg.jpg", {speed: 500});
        $('#license-field').mask("*9***-*****-*****-*****-***");
    </script>
@endsection