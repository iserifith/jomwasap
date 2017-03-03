@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Edit "{{ $url->alias }}" content.</div>
                <div class="panel-body">
					{!! Form::open(['method' => 'POST', 'route' => ['generate.update', $url->hashid]]) !!}

						{{ method_field('PUT') }}

					    <div class="form-group{{ $errors->has('alias') ? ' has-error' : '' }}">
					        {!! Form::label('alias', 'Short URL') !!}
					        <div class="input-group">
					        	<span class="input-group-addon">https://hi.jomwasap.my/</span>
					        	{!! Form::text('alias', $url->alias, ['class' => 'form-control']) !!}
					        </div>
					        <small class="text-danger">{{ $errors->first('alias') }}</small>
					        <p class="text-default">If left empty, system will automatically generate the alias.</p>
					    </div>

					    <div class="form-group{{ $errors->has('mobile_number') ? ' has-error' : '' }}">
					        {!! Form::label('mobile_number', 'Mobile Number *') !!}
					        {!! Form::text('mobile_number', $url->mobile_number, ['class' => 'form-control', 'required' => 'required']) !!}
					        <small class="text-danger">{{ $errors->first('mobile_number') }}</small>
					        <p class="text-default">Currently we only support Malaysia (+60) country code.</p>
					    </div>

						<div class="form-group{{ $errors->has('text') ? ' has-error' : '' }}">
						    {!! Form::label('text', 'Pretext Chat') !!}
						    {!! Form::textarea('text', $url->text, ['class' => 'form-control']) !!}
						    <small class="text-danger">{{ $errors->first('text') }}</small>
						</div>

					    <div class="btn-group pull-right">
					        {!! Form::reset("Reset", ['class' => 'btn btn-warning']) !!}
					        {!! Form::submit("Update", ['class' => 'btn btn-success']) !!}
					    </div>

					{!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
