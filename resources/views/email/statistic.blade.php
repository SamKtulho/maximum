@extends('layouts.app')

@section('content')

    <div class="container email-statistic">
        <div class="row">
            <div class="col-md-12">
                <table id="example" class="table table-striped table-bordered table-condensed" cellspacing="0" width="100%">
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
                            <td><a target="_blank" href="{{ $shortUrl->url }}">{{ $shortUrl->url }} </a></td>
                            <td> {{ isset($shortUrl->urlstats[0]) ? unserialize($shortUrl->urlstats[0]->stat)['allTime']['shortUrlClicks'] : '?' }} </td>
                            <td> <a target="_blank" href="//{{ $shortUrl->domain->domain }}">{{ $shortUrl->domain->domain }}</a> </td>
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
@endsection