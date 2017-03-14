
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h1>Emails</h1>

                <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                        @endif
                    @endforeach
                </div>

                {!! Form::open(['route' => 'email.store']) !!}
                <div class="form-group">
                    {!! Form::label('content') !!}
                    {!! Form::textarea('content', null, ['class'=>'form-control', 'placeholder' => "amonamius@gmail.com Валидный\nan5-dpnic@whoisprivacyprotect.dp Невалидный"] ) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Submit', ['class'=>'btn btn-primary']) !!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>
    </div>
@endsection