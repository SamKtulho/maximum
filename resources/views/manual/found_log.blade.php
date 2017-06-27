@extends('layouts.app')

@section('content')

    <div class="container manual-found-log">
        <div class="row">
            <div class="col-md-12">
                <table id="example" class="table table-striped table-bordered table-condensed" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Пользователь</th>
                        <th>Домен</th>
                        <th>Результат</th>
                        <th>Дата</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Пользователь</th>
                        <th>Домен</th>
                        <th>Результат</th>
                        <th>Дата</th>
                    </tr>
                    </tfoot>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <script src="{{ asset('js/manual_found_log.js') }}"></script>

@endsection