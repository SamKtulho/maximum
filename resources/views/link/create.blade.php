
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h1>Links</h1>

                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                        @endif
                    @endforeach
                </div> <!-- end .flash-message -->

                {!! Form::open(['route' => 'link.store']) !!}
                <div class="form-group">
                    {!! Form::label('content') !!}
                    {!! Form::textarea('content', null, ['class'=>'form-control'] ) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('source') !!} (указать источник доменов)
                    {!! Form::text('source', null, ['class'=>'form-control', 'placeholder' => 'Serp mp3'] ) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Submit', ['class'=>'btn btn-primary']) !!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>
    </div>
@endsection