
@extends('layouts.app')

@section('content')
    <div class="moderator_subdomain">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">

                    {!! Form::open(['route' => 'moderator.voteSubdomain']) !!}
                    <div class="form-group text-center">
                        <label class="form-check-label moderator-open-site">
                            {!! Form::checkbox('is_active', '1', false, ['class' => 'form-check-input', 'id' => 'is_active']) !!}
                            начать модерацию
                        </label>
                        {!! Form::button('Да', ['class'=>'btn btn-success btn-lg', 'value' => 1]) !!}
                        <span class="hor20 text-muted counter"></span>
                        {!! Form::button('Нет', ['class'=>'btn btn-danger btn-lg', 'value' => 2]) !!}
                        <span class="text-muted registrar"></span>

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