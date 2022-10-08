@extends('layout')
<style>
    .green-permission {
        color: #00a65a;
        font-size: 18px;
    }

</style>

@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-edit"></i> Roles / Edit #{{ $role->id }}</h1>
    </div>
@endsection

@section('content')
    @include('error')
    <br>
        <div class="col-md-12">
            <div class="col-md-12">
                {{-- @php(dd($permission)) --}}
                <div class="col-md-2 green-permission">
                    <label>{{ Form::checkbox('all', 1, false, []) }}Select All</label>
                </div>
                <div class="col-md-2 green-permission">
                    <label>{{ Form::checkbox('mod_list', 1, false, []) }} Listing</label>
                </div>
                <div class="col-md-2 green-permission">
                    <label>{{ Form::checkbox('mod_create', 1, false, []) }} Create</label>
                </div>
                <div class="col-md-2 green-permission">
                    <label>{{ Form::checkbox('mod_edit', 1, false, []) }} Edit</label>
                </div>
                <div class="col-md-2 green-permission">
                    <label>{{ Form::checkbox('mod_delete', 1, false, []) }} Delete</label>
                </div>
                <div class="col-md-2 green-permission">
                    <label>{{ Form::checkbox('mod_misc', 1, false, []) }} Miscellaneous</label>
                </div>
            </div>
            {!! Form::model($role, ['method' => 'PATCH', 'route' => ['roles.update', $role->id]]) !!}
            <div class="form-group">
                <h4><strong>Name:<strong></h4>
                {!! Form::text('display_name', null, ['placeholder' => 'Name', 'class' => 'form-control']) !!}
                <h4><strong>Description:<strong></h4>
                {!! Form::textarea('description', null, ['placeholder' => 'Description', 'class' => 'form-control', 'style' => 'height:100px']) !!}
            </div>
            <div class="form-group">
                <h2><strong>Permissions: </strong></h2>
                @php
                    $count = 1;
                @endphp
                @foreach ($chunked_permissions as $chunks)
                    <div class="row">
                        <div class="col-xs-12">
                            @foreach ($chunks as $key => $permission)
                                <div class="col-xs-4">
                                    <h3><b>{{ $count }}• &nbsp;{{ strtoupper($key) }}</b></h3>
                                    <ul>
                                        @foreach ($permission as $p)
                                            <li style="color: green;font-size:20px">
                                                <label>{{ Form::checkbox('permission[]', $p->id, in_array($p->id, $rolePermissions) ? true : false, ['class' => "name {$class} {$p->module}"]) }}
                                                    {{ $p->display_name }} </label>
                                                <br />
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @php
                                    $count++;
                                @endphp
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="well well-sm">
            <button type="submit" class="btn btn-primary">Save</button>
            <a class="btn btn-link pull-right" href="{{ route('roles.index') }}"><i
                    class="glyphicon glyphicon-backward"></i> Back</a>
        </div>
        {!! Form::close() !!}
@endsection
@section('scripts')
    <script type="text/javascript">
        $('input[name="all"]').on('click', function() {
            $('form input[type="checkbox"]').prop('checked', $(this).prop("checked"));
        });
        $('input[name^="mod_"]').on('click', function() {
            console.log($(this).attr('name').split('_')[1]);
            $('.' + $(this).attr('name').split('_')[1]).prop('checked', $(this).prop("checked"));
        });
    </script>
@endsection
