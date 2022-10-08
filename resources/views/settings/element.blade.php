<div class="col-xs-12">
    <div class="form-group">
    @php($name = $module."[".$key."]")
    @php($setting = collect($settings)->where('module',$module)->where('key', $key)->pluck('value')->first())
    @php($value = isset($setting) ? $setting : $element['default'])
    @php($class = !empty($element['class']) ? $element['class'] : 'form-control')
    @php($id = !empty($element['id']) ? $element['id'] : '')
    @if(!empty($element['title']))
    <label>
        {{$element['title']}}
        @if(!empty($element['tooltip']))
            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="{{$element['tooltip']}}">
                <img src="{{asset('assets/images/icons/help.png')}}" />
            </span>
        @endif
    </label>
    @endif
    @if ($element['type'] == \App\Setting::LABEL)
        <label class="form-control">{{$value}}</label>
    @elseif ($element['type'] == \App\Setting::TEXTBOX)
        <input type="text" name="{{$name}}" class="{{$class}}" id="{{$id}}" value="{{$value}}"/>
    @elseif ($element['type'] == \App\Setting::PASSWORD)
        <input type="password" name="{{$name}}" class="{{$class}}" id="{{$id}}" value="{{$value}}"/>
    @elseif ($element['type'] == \App\Setting::NUMBER)
        <input type="number" name="{{$name}}" class="{{$class}}" id="{{$id}}" value="{{$value}}"
        @if(!empty($element['min'])) min="{{$element['min']}}" @endif
        @if(!empty($element['max'])) max="{{$element['max']}}" @endif />
    @elseif ($element['type'] == \App\Setting::PHONE)
        <input type="text" name="{{$name}}" class="{{$class}}" id="{{$id}}" value="{{$value}}"/>
    @elseif ($element['type'] == \App\Setting::TEXTAREA)
        <textarea name="{{$name}}" class="{{$class}}" id="{{$id}}">{{$value}}</textarea>
    @elseif ($element['type'] == \App\Setting::CHECKBOX)
        <input type="checkbox" name="{{$name}}" class="{{$class}}" id="{{$id}}" value="1" {{($value)?"checked":""}}>
    @elseif($element['type'] == \App\Setting::SELECT)
        {!! Form::Select($name, $element['options'], $value, ['class'=> $class]) !!}
    @elseif($element['type'] == \App\Setting::SELECT2)
        <div class="row">
            {!! Form::Select($name.'[]', $element['options'], explode(",", $value), ['class'=> $class, 'multiple', 'style' => 'width:100%; padding-bottom:16px;']) !!}
        </div>
    @endif
    </div>
</div>