
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h1>Domains</h1>
                {!! Form::open(['route' => 'email.store']) !!}
                <div class="form-group">
                    {!! Form::label('content') !!}
                    {!! Form::textarea('content', null, ['class'=>'form-control'] ) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Submit', ['class'=>'btn btn-primary']) !!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>
    </div>
@endsection