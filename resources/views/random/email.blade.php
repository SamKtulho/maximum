
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))

                            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                        @endif
                    @endforeach
                </div> <!-- end .flash-message -->
                <h1>Письма</h1>
                {!! Form::open(['route' => 'random.email.store']) !!}
                <div class="form-group">
                    {!! Form::label('Заголовок') !!}
                    {!! Form::text('title', (!empty($title) ? $title : '{1 новое сообщение для владельца домена !dom_link!|Владельцу домена !dom_link!|У вас 1 сообщение владельцу домена !dom_link!|Предложение владельцу домена !dom_link!|Новое сообщение владельцу домена !dom_link!|Для владельца домена !dom_link!}'), ['class'=>'form-control'] ) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('Текст') !!}
                    {!! Form::textarea('content', (!empty($content) ? $content :
                    '
{Здравствуйте.|Добрый день.|Приветствую вас.}
<br><br>
{Если не ошибаюсь, вы являетесь владельцем домена|Мне вас порекомендовали как владельца домена|У нас к вам предложение, как к владельцу домена} !dom_link!.
<br><br>Мы предлагаем вам {установить|поставить} {нашу рекламу, которая|наш рекламный блок, который} будет {приносить|приносить прибыль} {приблизительно|ориентировочно|примерно} 30000 руб/месяц при {средней|обычной} посещаемости сайта 1000 мобильных пользователей в {день|сутки}.
<br><br>
Подробнее по ссылке !faq_link! {(Это Google документ) | (Это Гугл документ)}
                    '

                    ), ['class'=>'form-control']

                    ) !!}
                </div>
                <div class="form-group">
                    {!! Form::checkbox('edomain[]', 'mail', true) !!}
                    {!! Form::label('@mail.ru') !!}
                    <span style="margin-left: 15px;"></span>
                    {!! Form::checkbox('edomain[]', 'yandex', true) !!}
                    {!! Form::label('@yandex.*') !!}
                    <span style="margin-left: 15px;"></span>
                    {!! Form::checkbox('edomain[]', 'gmail', true) !!}
                    {!! Form::label('@gmail.com') !!}
                    <span style="margin-left: 15px;"></span>
                    {!! Form::checkbox('edomain[]', 'other', true) !!}
                    {!! Form::label('Остальные') !!}
                    <span style="margin-left: 25px;"></span>
                    {!! Form::label('ТИЦ') !!}

                    {!! Form::select('tic', [1 => 'Любой', 10 => '10', 20 => 20, 30 => 30, 40 => 40, 50 => '50-70', 80 => 80, 90 => 90, 100 => '100-200'], '10') !!}
                </div>
                <div class="form-group">
                    {!! Form::button('Submit', ['class'=>'btn btn-primary btn-main']) !!}
                </div>
                {!! Form::close()!!}

                <div id="result">
                    <div id="email"></div>
                    <h4></h4>
                    <div id="body"></div>
                </div>

            </div>
        </div>
    </div>
    <script>
        $( document ).ready(function() {
            $('.btn-main').click(function () {
                var data = $( "form" ).serializeArray();
                $.post( "/random/email/store", data, function( data ) {
                    $( "#result #email" ).html( data.response[3] );
                    $( "#result h4" ).html( data.response[1] );
                    $( "#result #body" ).html( data.response[0] );
                });
            });
        });
    </script>
@endsection