@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jq-2.2.4/dt-1.10.13/datatables.min.css"/>
                <script src="{{ asset('js/bootstrap.min.js') }}"></script>
                <script type="text/javascript" src="{{ URL::asset('//cdn.datatables.net/v/bs/jq-2.2.4/dt-1.10.13/datatables.min.js') }}"></script>

                <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Ссылка</th>
                        <th>Клики</th>
                        <th>Домен</th>
                        <th>Email</th>
                        <th>Создатель</th>
                        <th>Дата</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Ссылка</th>
                        <th>Клики</th>
                        <th>Домен</th>
                        <th>Email</th>
                        <th>Создатель</th>
                        <th>Дата</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    @foreach ($shortUrls as $shortUrl)
                        <tr>
                            <td><a href="{{ $shortUrl->url }}">{{ $shortUrl->url }} </a></td>
                            <td> {{ isset($shortUrl->urlstats[0]) ? unserialize($shortUrl->urlstats[0]->stat)['allTime']['shortUrlClicks'] : '?' }} </td>
                            <td> {{ $shortUrl->domain->domain }} </td>
                            <td> {{ $shortUrl->domain->emails[0]->email }} </td>
                            <td> {{ $shortUrl->user->name }} </td>
                            <td> {{ $shortUrl->created_at }} </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        } );
    </script>
@endsection