@extends('layouts.app')

@section('content')

    <div class="container link-moderation-log">
        <div class="row">
            <div class="col-md-12">
                <table id="example" class="table table-striped table-bordered table-condensed" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Модератор</th>
                        <th>Домен</th>
                        <th>Результат</th>
                        <th>Дата</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Модератор</th>
                        <th>Домен</th>
                        <th>Результат</th>
                        <th>Дата</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td> {{ $log->user->name }} </td>
                            <td> <a target="_blank" href="//{{ $shortUrl->domain->domain }}">{{ $log->domain->domain }}</a> </td>
                            <td> {{ $log->result }} </td>
                            <td> {{ $log->created_at }} </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <script src="{{ asset('js/link_moderation_log.js') }}"></script>

@endsection