
@extends('layouts.app')

@section('content')
    <div class="manual_report">
        <div class="container">
            <div class="row">
                <div class="col-md-11">
                    <h2>Отчет по датам</h2>
                    <table class="table table-striped">
                        <thead>
                        <tr class="text-center">
                            <th></th>
                            <th class="text-center">Сегодня</th>
                            <th class="text-center">Вчера</th>
                            @if(!empty($reportByDate[0]))
                                @foreach ($reportByDate[0] as $date => $count)
                                    @if(strtotime(date('Y-m-d', strtotime('-' . 0 . ' days'))) == $date
                                         || strtotime(date('Y-m-d', strtotime('-' . 1 . ' days'))) == $date
                                         )
                                        @continue;
                                    @endif
                                    <th class="text-center">{{ date('Y-m-d', $date) }}</th>
                                @endforeach
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($reportByDate as $name => $row)
                            <tr>
                                <td>{{ \App\Models\Shorturl::getActionMap()[$name] }}</td>
                                @foreach ($row as $date => $count)
                                    <td class="text-center"> {{ $count }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <h2>Отчет по пользователям</h2>
                    <table class="table table-striped">
                        <thead class="text-center">
                        <tr>
                            <th>Имя</th>
                            <th>Тема</th>
                            <th class="text-center">Найдено</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportByUser as $row)
                                @if (!$row->action)
                                    @continue
                                @endif
                                <tr>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ \App\Models\Shorturl::getActionMap()[$row->action] }}</td>
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