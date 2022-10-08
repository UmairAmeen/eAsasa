<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="aspireanalytica.com/easasa">
    <title>{{(isset($title))?$title." - ":""}} eAsasa</title>
    <!-- Bootstrap core CSS -->
    <link href="{{asset('assets/css/bootstrap.css')}}?v=3.3.7" rel="stylesheet">
    <!--external css-->
    <link href="{{asset('assets/fontawesome-web/css/all.css')}}" rel="stylesheet"/>
    <!-- Custom styles for this template -->
    <!-- icon set -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('branding/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('branding/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('branding/favicon-16x16.png')}}">
    <link rel="manifest" href="{{asset('branding/site.webmanifest')}}">
    <link rel="mask-icon" href="{{asset('branding/safari-pinned-tab.svg')}}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <!-- end icon set -->
    <link href="{{asset('assets/css/style.css')}}?v=2.23" rel="stylesheet">
    <link href="{{asset('assets/css/style-responsive.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/menuNew.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/alertify.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css//themes/bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/bootstrap.min.css.map')}}" rel="stylesheet" type="text/css">
    {{-- dev express --}}
    <link rel="stylesheet" href="{{asset('assets/css/dx.light.css')}}?v=0.4"> 


    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/responsive/2.2.8/css/responsive.dataTables.min.css">
    <script type="text/javascript">
        (function (e, t) {
            var n = e.amplitude || {_q: [], _iq: {}};
            var r = t.createElement("script");
            r.type = "text/javascript";
            r.async = true;
            r.src = "https://cdn.amplitude.com/libs/amplitude-4.4.0-min.gz.js";
            r.onload = function () {
                if (e.amplitude.runQueuedFunctions) {
                    e.amplitude.runQueuedFunctions()
                } else {
                    console.log("[Amplitude] Error: could not load SDK")
                }
            };
            var i = t.getElementsByTagName("script")[0];
            i.parentNode.insertBefore(r, i);
            function s(e, t) {
                e.prototype[t] = function () {
                    this._q.push([t].concat(Array.prototype.slice.call(arguments, 0)));
                    return this;
                }
            }
            var o = function () {this._q = [];return this};
            var a = ["add", "append", "clearAll", "prepend", "set", "setOnce", "unset"];
            for (var u = 0; u < a.length; u++) {s(o, a[u])}
            n.Identify = o;
            var c = function () {this._q = [];return this};
            var l = ["setProductId", "setQuantity", "setPrice", "setRevenueType", "setEventProperties"];
            for (var p = 0; p < l.length; p++) {s(c, l[p])}
            n.Revenue = c;
            var d = ["init", "logEvent", "logRevenue", "setUserId", "setUserProperties", "setOptOut", "setVersionName", "setDomain", "setDeviceId", "setGlobalUserProperties", "identify", "clearUserProperties", "setGroup", "logRevenueV2", "regenerateDeviceId", "logEventWithTimestamp", "logEventWithGroups", "setSessionId", "resetSessionId"];
            function v(e) {
                function t(t) {
                    e[t] = function () {
                        e._q.push([t].concat(Array.prototype.slice.call(arguments, 0)))
                    }
                }
                for (var n = 0; n < d.length; n++) {t(d[n])}
            }

            v(n);
            n.getInstance = function (e) {
                e = (!e || e.length === 0 ? "$default_instance" : e).toLowerCase();
                if (!n._iq.hasOwnProperty(e)) {
                    n._iq[e] = {_q: []};
                    v(n._iq[e])
                }
                return n._iq[e]
            };
            e.amplitude = n
        })(window, document);
        amplitude.getInstance().init("b0abf517ccb6c0c8e833b1583876bd4b");

        @if (Auth::user())
        amplitude.getInstance().setUserId('{{Auth::user()->name}}');
        @endif
        amplitude.getInstance().logEvent('Working at: {{url()->current()}}');
    </script>
    <!-- Support ended 11/Jan/2020 too much batamezi -->
    <!-- <script>
        Userback = window.Userback || {};
        Userback.access_token = '8040|13421|RTVAtBYtib2RYDUTVg4ojc2CYE9xrSyUFBm25EU8XZoOT91CWM';
        (function(id) {
            var s = document.createElement('script');
            s.async = 1;s.src = 'https://static.userback.io/widget/v1.js';
            var parent_node = document.head || document.body;parent_node.appendChild(s);
        })('userback-sdk');
    </script> -->
    <style type="text/css">
        .table tbody {font-weight: bolder;font-size: 14px;color: #000 !important;}
        span.select2.select2-container.select2-container--default.select2-container--focus, .select2-container--open {
            border: 2px solid #FF5722;
        }
        a.text-muted:focus, a.text-muted:hover {border: 1px solid #FF5722;}
    </style>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    @yield('css')
</head>
<div id="pjax-container"></div>
<body>
@if(isset($load_head) && $load_head)
<section id="container">
    <header class="header black-bg">
        <div class="sidebar-toggle-box">
            <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
        </div>
        <!--logo start-->
        <a href="{{url('/')}}" class="logo"><b>eAsasa</b></a>
        <!--logo end-->
        <div class="nav notify-row" id="top_menu">
            <!--  notification start -->
            <ul class="nav top-menu">
                <!-- settings start -->
                <li class="dropdown" title="License Information" data-placement="bottom" data-toggle="tooltip">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="index.html#">
                        <i class="fa fa-tasks"></i>
                        <!-- <span class="badge bg-theme">4</span> -->
                    </a>
                    <ul class="dropdown-menu extended tasks-bar">
                        <div class="notify-arrow notify-arrow-green"></div>
                        <li>
                            <p class="green">License Information</p>
                        </li>
                        <li>
                            <?php $days = Session::get('license_info')['days_left_in_expiry']; ?>
                            <a href="#">
                                <div class="task-info">
                                    <div class="desc">License Validity</div>
                                <!-- <div class="percent">{{round(($days * 100)/30,0)}}%</div> -->
                                </div>
                                <div class="progress progress-striped">
                                    <div class="progress-bar progress-bar-success" role="progressbar"
                                         aria-valuenow="{{$days}}" aria-valuemin="0" aria-valuemax="30"
                                         style="width: {{($days * 100)/30}}%">
                                        <!-- <span class="sr-only">60% Complete (warning)</span> -->
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                @if ($days > 1 )
                                    <span>Your License is valid for {{$days}} Days</span>
                                @else
                                    <span>Your License is valid for {{$days}} Day</span>
                            @endif
                        </li>
                        </a>
                        <li>
                            <a href="">
                                <span>Software Version: {{getSoftwareVersion()}}</span> </a>
                        </li>
                        <li>
                            <a href="">
                                <span>Helpline: +92-345-4777-487</span> </a>
                        </li>
                        <li>
                            <a href="https://aspireanalytica.com/easasa">
                                Powered By eAsasa (Aspire Analytica [Pvt] Ltd) &copy; <?=date('Y')?></a>
                        </li>
                    </ul>
                </li>
                <!-- settings end -->
                <!-- inbox dropdown start-->
                <?php
                $notice = stock_notice();
                ?>
                <li id="header_inbox_bar" title="Notifications" data-placement="bottom" data-toggle="tooltip"
                    class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="fab fa-stack-overflow"></i>
                        @if (count($notice) > 0)
                            <span class="badge bg-theme04">{{count($notice)}}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu extended inbox">
                        <div class="notify-arrow notify-arrow-green"></div>
                        <li>
                            <?php $c = 0 ?>
                            @if (count($notice) > 0)
                                <p class="red">{{count($notice) }} Low Stock Notice</p>
                            @else
                                <p class="green">No Notification</p>
                            @endif
                        </li>
                        @foreach ($notice as $value)
                            @if ($c++ > 5)
                                <?php break; ?>
                            @endif
                            <li>
                                <a href="/products/{{$value->id}}">
                                <!-- <span class="photo"><img alt="avatar" src="{{asset('assets/img/ui-zac.jpg')}}"></span> -->
                                    <span class="subject">
                                    <span class="from">{{$value->name}}</span>
                                    <span class="time">{{date('d/m/Y', strtotime($value->updated_at))}}</span>
                                    </span>

                                </a>
                            </li>
                        @endforeach
                        @if (count($notice) > 5)
                            <li>
                                <a class="btn btn-info" href="/products_out_of_stock">
                                <!-- <span class="photo"><img alt="avatar" src="{{asset('assets/img/ui-zac.jpg')}}"></span> -->
                                    <span class="subject">
                                    </span>
                                    <span class="message">
                                        You have {{count($notice)}} products out of stock - View All
                                    </span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                <!-- inbox dropdown end -->
                <li class="dropdown" title="Full Screen" data-placement="bottom" data-toggle="tooltip"><a
                            id="videoElement" href="#"><i class="fa fa-desktop"></i></a></li>
                <li>
                    @if ($days < 5 )
                        <span class=" alert alert-danger">Your License Key is Expiring in {{$days}} days, Contact at +92 345 4777 487</span>
                    @endif
                </li>
            </ul>
            <!--  notification end -->
        </div>
        <div class="top-menu">
            <ul class="nav pull-right top-menu">
                <li><a class="logout" href="{{URL('/logout')}}">Logout</a></li>
            </ul>
        </div>
    </header>
    <!--header end-->
    <!-- **********************************************************************************************************************************************************
    MAIN SIDEBAR MENU
    *********************************************************************************************************************************************************** -->
    <!--sidebar start-->
    <aside>
        <div id="sidebar" class="nav-collapse ">
            <!-- sidebar menu start-->
            <ul class="sidebar-menu" id="nav-accordion">

                <p class="centered">
                    <a href="{{URL('profile')}}">
                        <img src="{!! Auth::user()->photo() !!}" class="img-circle" width="60">
                    </a>
                </p>
                <h5 class="centered">{{Auth::user()->name}}</h5>

                @include ('menu')

            </ul>
            <!-- sidebar menu end-->
        </div>
    </aside>
@endif
     <section @if(isset($load_head) && $load_head) id="main-content" @endif>
         <section class="wrapper">
             @yield('header')
             @yield('content')
         </section><!-- /.container -->
    </section>
    <div id="loader"></div>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="{{asset('assets/js/jquery.js')}}"></script>
    <script src="{{asset('assets/js/menuNew.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('/assets/js/jquery.maskedinput.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/select2.full.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/alertify.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/jquery.nicescroll.js')}}" type="text/javascript"></script>
    <script class="include" type="text/javascript" src="{{asset('assets/js/jquery.dcjqaccordion.2.7.js')}}"></script>
    <script src="{{asset('/assets/js/jquery.scrollTo.min.js')}}"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!-- <script src="{{asset('assets/js/ie10-viewport-bug-workaround.js')}}"></script> -->
    <script src="{{asset('assets/js/common-scripts.js')}}?v=1"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.html5.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/datatables/jszip.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/datatables/pdfmake.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/datatables/vfs_fonts.js')}}"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.8/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="{{asset('assets/js/dx.all.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function(e) {
            $('[data-toggle="tooltip"]').tooltip();
            $('.select-2').select2();
        });
        function launchFullScreen(element) {
            if(element.requestFullScreen) {
                element.requestFullScreen();
            } else if(element.mozRequestFullScreen) {
                element.mozRequestFullScreen();
            } else if(element.webkitRequestFullScreen) {
                element.webkitRequestFullScreen();
            }
        }
        function load_start() {
            $("body").addClass("loading");
            $("#loader").fadeIn( "slow", function(){$(this).show()});
        }
        function load_end() {
            $("#loader").fadeOut( "slow", function(){$(this).hide()});
            $("body").removeClass("loading");
        }
        // Launch fullscreen for browsers that support it!
        $("#videoElement").click(function (e) {
            e.preventDefault();
            launchFullScreen(document.documentElement); // the whole page
        });
        var old_request = false;
        function init() {}
        var form;
        $(".datatable").DataTable({"deferRender": true,responsive: true,"order": [[0,"desc"]],"lengthMenu": [[10,25,50,100,-1],[10,25,50,100,"All"]]});
        $(document).on("submit", "form", function (e) {
            form = $(this);
            if ($(form).hasClass('no-ajax')) {return;}
            $("#submit_btn").addClass('disabled');//disable submit button
            e.preventDefault();
            load_start();
            try {
                if(old_request) {
                    old_request.abort();
                }                
            } catch (e) {
                console.log(e);//nothing to do
            }
            old_request = $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                method: "POST",
                dataType: "json"
            }).done(function(e) {
                load_end();
                $(form).find("#log").removeClass();
                $("#submit_btn").removeClass('disabled');
                if(e.message)
                    {
                        $(form).find("#log").addClass( "alert alert-success" );
                        $(form).find("#log").html(e.message);
                    }
                if(e.sms_message){
                    if(e.sms_message == 'SMS Sent')
                    {
                        $(form).find("#error_log").addClass( "alert alert-success" );
                        $(form).find("#error_log").html(e.sms_message);
                    }
                    else{
                        $(form).find("#error_log").addClass( "alert alert-danger" );
                        $(form).find("#error_log").html(e.sms_message);
                    }  
                }
                if(e.sms_response)
                {
                    var r_message = e.sms_response.message;
                    var m_type = e.sms_response.type;
                    if(m_type == 'success')
                        {
                            if(r_message.success == '0')
                            {
                                $(form).find("#sms_log").addClass( "alert alert-danger" );
                                $(form).find("#sms_log").html(r_message);
                            }
                            else{
                                $(form).find("#sms_log").addClass( "alert alert-success" );
                                $(form).find("#sms_log").html(r_message);
                            }    
                        }
                        else{
                            $(form).find("#sms_log").addClass( "alert alert-danger" );
                            $(form).find("#sms_log").html(r_message);
                        }
                }
                if (e.action) {
                    setTimeout(function(){callback(e.action, e.do, e.val, e.text, e.script);}, 100);
                }
            }).error(function(e) {
                load_end();
                $(form).find("#log").removeClass();
                $("#submit_btn").removeClass('disabled');
                $(form).find("#log").addClass( "alert alert-danger" );
                IS_JSON = false;
                var obj = "";
                try {
                    var obj = $.parseJSON(e.responseText);
                    IS_JSON = true;
                } catch(err) {
                    IS_JSON = false;
                }
                if (IS_JSON) {
                    var errhtml = "";
                    // var obj = jQuery.parseJSON(e.responseText);
                    $.each(obj, function(key,value) {
                        // alert(value.com);
                        errhtml = errhtml + value + "<br>";
                    });
                    $(form).find("#log").html(errhtml);
                } else {
                    try {
                        if (e.responseJSON.message) {
                            $(form).find("#log").html(e.responseJSON.message);
                        } else {
                            $(form).find("#log").html("Some Error Occured, Try Again");
                        }                        
                    } catch(err) {
                        $(form).find("#log").html("Some Error Occured, Try Again or Call our helpline");
                    }
                }
            });
        });
        function callback(action, data, vali=false, text=false, extra_script=false) {
            switch(action) {
                case 'redirect':
                    load_start();
                    window.location = data;
                    return;
                case 'reload':
                    window.location.reload();
                    return;
                case 'reset':
                  resetForm(data, vali, text);
                    if ($("#warehouse").length > 0) {
                        // $("#warehouse").val("4").trigger("change");
                    }
                    break;
                case 'same_state_datable_reload':
                    var table = $(data).DataTable();
                    table.ajax.reload( null, false );

                    // if instance not null or undefined
                    if (typeof(instance) != "undefined" && instance != null) {
                        instance.refresh();
                    }
                break;
                case 'dismiss':
                    if (extra_script) {
                        extra_script();
                    }
                    if (text) {
                        // debugger
                        $(data).append('<option value="'+vali+'" >'+text+"</option>");
                        $(data).trigger('change'); 
                    }
                    // debugger;
                    $(data).val(vali).trigger('change');
                    $(".modal").modal("hide");
                    $(".modal").find("form").each(function(index, ele){
                        $(ele).find("#log").html("");
                        $(ele)[0].reset();
                    });
                    return;
                case 'update':
                    // $(data).dataTable('refresh');
                    var table = $(data).DataTable();
                    table.ajax.reload();
                    $("[role=dialog]").modal('hide');
                    if (typeof(instance) != "undefined" && instance != null) {
                        instance.refresh();
                    }
                    return;
            }
        }
        $(".sub-menu").click(function(e) {
            $("html, body").animate({ scrollTop: 0 }, "slow"); 
            load_start();
        });
        function fetch_show(url, set_to, showSpareModal=false) {
            $.ajax({
                url: url,
                method: "GET",
                dataType: "HTML"
            }).done(function(e) {
                // load_end();
                if (showModal) {
                    $("#spareModal").modal('show');
                }
                $(set_to).html(e);
            }).error(function(e) {
                if (showModal) {
                    $("#spareModal").modal('show');
                }
                $(set_to).find("#log").removeClass();
                $(set_to).find("#log").addClass( "alert alert-danger" );
                IS_JSON = false;
                var obj = "";
                try {
                    var obj = $.parseJSON(e.responseText);
                    IS_JSON = true;
                } catch(err) {
                    IS_JSON = false;
                }
                if (IS_JSON) {
                    var errhtml = "";
                    // var obj = jQuery.parseJSON(e.responseText);
                    $.each(obj, function(key,value) {
                        // alert(value.com);
                        errhtml = errhtml + value + "<br>";
                    });
                    $(set_to).find("#log").html(errhtml);
                } else {
                    try {
                        if (e.responseJSON.message) {
                            $(set_to).find("#log").html(e.responseJSON.message);
                        } else {
                            $(set_to).find("#log").html("Some Error Occured, Try Again");
                        }
                    } catch(err) {
                        $(set_to).find("#log").html("Some Error Occured, Try Again or Call our helpline");
                    }
                }
            });
        }
    </script>
</section>
    @yield('scripts')
    <script>
    $('.date-picker').datepicker({format: 'dd-mm-yyyy',});
    init();
    function updateItemCount() {
        var qty = $("#appendMe").children().find("input[name^=quantity]");
        var count = 0;
        $.each(qty, function (index, val) {
            count += parseFloat($(val).val());
        });
        $("#item_count").html(count + "  enteries: " + ($("#appendMe").children().length - 1));
    }
    function set_user_balance(value, place, supplier=false) {       
        var url = "/get_customer_balance/";
        if (!value) {
            $(place).html("0");
        }
        if(supplier)
        {
            url = "/get_supplier_balance/";
        }
        $.ajax({
            url: url,
            data: {
                'customer_id': value,
                }
        }).done(function (data) {
            $(place).html(thousands_separators(parseFloat(data).toFixed(2)));
        }).error(function (d) {
            alertify.error('Some error occurred, try again').dismissOthers();
            $(place).html('Unable to get Customer Balance, try again');
        });
    }
    function resetForm(data, vali, text) {
        $("form")[0].reset();
        $(".cleanBtn").each(function (index, element) {
            $(element).click();
        });
        $(".customer").val("{{($request)?$request->customer:''}}").trigger("change");
        $("#previous_balance").html("0");
        $("input[name=date]").select();
        if (data) {
            var myWindow = window.open(data, "", "width=500,height=500");
        }
    }
    function thousands_separators(num) {
        var num_parts = num.toString().split(".");
        num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return num_parts.join(".");
    }
    $(document).ready(function () {
        if ($("#warehouse").length > 0) {
            // $("#warehouse").val("4").trigger("change");
        }
        $.fn.dataTable.ext.errMode = 'none';
    });
</script>
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" id="spareModal" class="modal fade"  data-backdrop="static"> 
  <div class="modal-dialog" style="width: 80%">
   <div class="modal-content col-md-12"></div>
  </div>
</div>
</body>
</html>
