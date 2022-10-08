@extends('layout')
@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css" rel="stylesheet">
  <style>
    .tabs-left { border-bottom: none; padding-top: 2px; border-right: 1px solid #ddd; }
    .tabs-left>li { float: none; margin-bottom: 2px; margin-right: -1px; }
    .tabs-left>li.active>a,
    .tabs-left>li.active>a:hover,
    .tabs-left>li.active>a:focus { border-bottom-color: #ddd; border-right-color: transparent; }
    .tabs-left>li>a { border-radius: 4px 0 0 4px; margin-right: 0; display:block; }
    /* .content-panel { min-height: 59vh;} */
  </style>
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="fa fa-gear"></i> Settings</h1>
    </div>
@endsection

@section('content')
    @include('error')
    <div class="row">
        <div class="col-xs-12">
            <form action="{{ route('settings.store') }}" method="POST">
                <div class="row">
                    <div class="col-xs-3">
                        {{-- <input type="text" class="form-control" name="searchSetting"> --}}
                        <ul class="nav nav-tabs tabs-left">
                            @foreach (array_keys($defaultSettings) as $index => $module)
                                <li class="{{$index == 0 ? 'active' : ''}}">
                                    <a href="#{{$module}}" data-toggle="tab">{{isset(\App\Setting::$ModuleList[$module]) ? \App\Setting::$ModuleList[$module] : ""}}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-xs-9 content-panel">
                        <div class="tab-content">
                            @foreach (array_keys($defaultSettings) as $index => $module)
                                <div class="tab-pane {{$index == 0 ? 'active' : ''}}" id="{{$module}}">
                                    @foreach ($defaultSettings[$module] as $key => $element)
                                    @if(in_array($key,$sms_keys) && !env('SHOW_SMS_CREDENTIALS'))
                                        @continue
                                    @endif
                                        @include('settings.element')
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="/clearcache" class="btn btn-warning" onclick="alertify.success('Cache Cleared')">Clear All Cache</a>
                            <a href="/clearSession" class="btn btn-danger" onclick="alertify.success('Session Cleared')">Clear Session</a>
                            <a href="{{url('check_notification')}}" class="btn btn-success">Check Notifications</a>
                            <a class="btn btn-link pull-right" href="{{ route('settings.index') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    {{--
    <div class="row">
        <div class="col-md-12">

            <form action="{{ route('settings.store') }}" method="POST">
            <div id="log"></div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                @foreach ($settings as $setting)
                @if ($setting->type == "static")
                <span class="card">
                    <!-- <ul class="list-group list-group-flush"> -->
                        <span class="list-group-item"><b>{{$setting->name}}:</b> {{$setting->value}}</span>
                    <!-- </ul> -->
                </span>
                @elseif ($setting->type == "number")
                <div class="form-group">
                    <label>{{($setting->name)?$setting->name:$setting->key}}:</label>
                    <input type="{{$setting->type}}" name="{{$setting->key}}"  class="form-control" value="{{$setting->value}}">
                </div>
                @elseif ($setting->type == "textarea")
                <div class="form-group">
                    <label>{{($setting->name)?$setting->name:$setting->key}}:</label>
                    <textarea name="{{$setting->key}}"  class="form-control">{{$setting->value}}</textarea>
                </div>
                @elseif ($setting->type == "checkbox")
                <div class="form-group">
                    <label>{{($setting->name)?$setting->name:$setting->key}}:</label>
                    <input type="checkbox" name="{{$setting->key}}"  class="form-control" value="1" {{($setting->value)?"checked":""}}>
                </div>
                @else
                <div class="form-group">
                    <label>{{($setting->name)?$setting->name:$setting->key}}:</label>
                    <input type="{{$setting->type}}" name="{{$setting->key}}" class="form-control" placeholder="{{$setting->name}}" value="{{$setting->value}}" accept="image/*">
                </div>
                @endif
                @endforeach
            


                
                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="/clearcache" class="btn btn-warning" onclick="alertify.success('Cache Cleared')">Clear All Cache</a>
                     <a href="{{url('check_notification')}}" class="btn btn-success">Check Notifications</a>
                    <a class="btn btn-link pull-right" href="{{ route('settings.index') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
                </div>
            </form>

        </div>
    </div>
    --}}


@endsection
@section('scripts')
  <script src="{{asset('assets/js/bootstrap-switch.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.0/moment.min.js" integrity="sha512-Izh34nqeeR7/nwthfeE0SI3c8uhFSnqxV0sI9TvTcXiFJkMd6fB644O64BRq2P/LA/+7eRvCw4GmLsXksyTHBg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="{{asset('/assets/js/tinymce.min.js')}}"></script>
  <script>
        const updateFiscalDate = function (element) {
            const now = new moment();
            const start = $('#' + element + 'FiscalStart');
            const end = $('#' + element + 'FiscalEnd');
            let data = start.val().split('-');
            const date = new moment(data[0] + '-' + data[1] + '-' + now.year(), 'DD-MM-YYYY');
            // start.val(date.format('DD-MM')).data('daterangepicker').elementChanged();
            // start.trigger('change');
            date.add(1, 'year').subtract(1, 'day');
            end.val(date.format('DD-MM'));
        };

    $('.readonly').attr('readonly', 'readonly');
    $('.dm_picker').datepicker({format: 'dd-mm'});
    $('#accountsFiscalStart').on('change', function () {
        updateFiscalDate('accounts');
    });
    
    $('.select2').select2();
    tinymce.init({
        selector:'textarea',
        plugins: 'lists hr table image textcolor colorpicker',
        toolbar: 'undo redo | hr | styleselect | fontselect | fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | table | link image',
        fontsize_formats: "8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 24pt 28pt 30pt 36pt 48pt 60pt 72pt 96pt",
        menubar: false,
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: function (cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.onchange = function () {
                var file = this.files[0];

                var reader = new FileReader();
                reader.onload = function () {
                    /*
                      Note: Now we need to register the blob in TinyMCEs image blob
                      registry. In the next release this part hopefully won't be
                      necessary, as we are looking to handle it internally.
                    */
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);

                    /* call the callback and populate the Title field with the file name */
                    cb(blobInfo.blobUri(), { title: file.name });
                };
                reader.readAsDataURL(file);
            };
            input.click();
        },
        table_class_list: [
            {title: 'None', value: ''},
            {title: 'No Borders', value: 'table_no_borders'},
            {title: 'Red borders', value: 'table_red_borders'},
            {title: 'Blue borders', value: 'table_blue_borders'},
            {title: 'Green borders', value: 'table_green_borders'}
        ],
        setup: function (editor) {
        editor.on('change', function () {
            tinymce.triggerSave();
        });} });
    $(document).ready(function(){
        $("input[type='checkbox']").wrap('<div class="switch" />').parent().bootstrapSwitch();
    });
  </script>
@endsection
