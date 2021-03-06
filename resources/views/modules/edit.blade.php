@extends('../app')
@section('content')
<div class="col-sm-12">
    <div class="panel panel-default">
        <div class="panel-body">

    {!! Form::model($module,['method' => 'PATCH','route'=>['modules.update',$module->mod_id]]) !!}
        @include("blocks.error_block")
    <div class="form-group">
        {!! Form::label('Module Name', 'Module Name:') !!}
        {!! Form::text('mod_name',null,['class'=>'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('Module Url', 'Module Url:') !!}
        {!! Form::text('mod_url',null,['class'=>'form-control']) !!}
    </div>

        <div class="form-group">
        {!! Form::label('Status', 'Status:') !!}
        {!! Form::select('mod_status', array('1' => 'Active', '0' => 'In-Active'), $module->mod_status ,['class'=>'form-control']) !!}
    </div>
    <div class="form-group">
        <a href="{{ url('modules')}}" class="btn btn-purple">Back</a>
        {!! Form::submit('Update', ['class' => 'btn btn-info btn-single  pull-right']) !!}
        {!! Form::reset('Reset', ['class' => 'btn btn-orange btn-single  pull-right']) !!}

    </div>
    {!! Form::close() !!}

</div>
</div>
</div>
@stop
