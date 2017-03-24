
@extends('layouts.app')

@section('content')
    <div class="moderator">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">

                    {!! Form::open(['route' => 'moderator.vote']) !!}
                    <div class="form-group text-center">
                        {!! Form::button('Да', ['class'=>'btn btn-success btn-lg', 'value' => 1]) !!}
                        <span class="hor20 text-muted"></span>
                        {!! Form::button('Нет', ['class'=>'btn btn-danger btn-lg', 'value' => 2]) !!}
                    </div>
                    <div class="form-group text-center">
                        <a target="_blank" href="" id="link"></a>
                    </div>
                    {!! Form::hidden('domain_id', '', ['id' => 'domain_id']) !!}
                    {!! Form::hidden('vote', '', ['id' => 'vote']) !!}
                    {!! Form::close()!!}
                </div>
            </div>
        </div>
        <iframe id="iframe" scrolling="yes" width=100% height="1028" src=""></iframe>
    </div>
@endsection