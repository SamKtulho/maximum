
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h1>Domains</h1>
                {!! Form::open(['route' => 'domain.store']) !!}
                @include('domain._form')
                {!! Form::close()!!}
            </div>
        </div>
    </div>
@endsection