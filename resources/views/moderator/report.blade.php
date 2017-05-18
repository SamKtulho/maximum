
@extends('layouts.app')

@section('content')
    <div class="moderator_report">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Имя</th>
                            <th>Тема</th>
                            <th>Промодерировано</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($report as $row)
                                <tr>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ \App\Models\ModerationLog::getTypeMap()[$row->type] }}</td>
                                    <td class="text-center"> {{ $row->total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <iframe id="iframe" scrolling="yes" width=100% height="1028" src=""></iframe>
    </div>
@endsection