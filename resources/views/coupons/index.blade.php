
@extends('layouts.app')

@section('content')

    <div class="container coupons">
        @include('coupons.form')
    </div>

    <script src="{{ asset('js/coupons.js') }}"></script>

@endsection