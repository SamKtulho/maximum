
@extends('layouts.app')

@section('content')
    <div class="container random-manual-subdomain">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="flash-message">
                    <p class=""> </p>
                </div> <!-- end .flash-message -->
                {!! Form::open(['route' => 'random.manualSubdomain']) !!}
                <div class="vert25">
                    {!! Form::label('Настройки письма:') !!}
                    {!! Form::button('Показать', ['class'=>'btn btn-sm show-button']) !!}
                </div>
                <div class="mail-settings hide">
                    <div class="form-group">
                        {!! Form::label('ФИО') !!}
                        {!! Form::text('fio', (!empty($template['fio']) ? $template['fio'] : '{Петров|Игнатов|Коршунов|Малых} {Олег|Михаил|Евгений|Александр} {Валерьевич|Владимирович|Дмитриевич}'), ['class'=>'form-control', 'style' => 'font-family: sans-serif;'] ) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('Email') !!}
                        {!! Form::text('email', (!empty($template['email']) ? $template['email'] : '{fumka@gmail.com|muhol1@mail.ru|frenke34@yandex.ru|tropor_0@gmail.com}'), ['class'=>'form-control', 'style' => 'font-family: sans-serif;'] ) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('Заголовок письма') !!}
                        {!! Form::text('title', (!empty($template['title']) ? $template['title'] : '{1 новое сообщение для владельца домена !dom_link!|Владельцу домена !dom_link!|У вас 1 сообщение владельцу домена !dom_link!|Предложение владельцу домена !dom_link!|Новое сообщение владельцу домена !dom_link!|Для владельца домена !dom_link!}'), ['class'=>'form-control', 'style' => 'font-family: sans-serif;'] ) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('Текст письма') !!}
                        {!! Form::textarea('content', (!empty($template['content']) ? $template['content'] :
                        '
    {Здравствуйте.|Добрый день.|Приветствую вас.}
    <br><br>
    {Если не ошибаюсь, вы являетесь владельцем домена|Мне вас порекомендовали как владельца домена|У нас к вам предложение, как к владельцу домена} !dom_link!.
    <br><br>
    Мы предлагаем вам {установить|поставить} {нашу рекламу, которая|наш рекламный блок, который} будет {приносить|приносить прибыль} {приблизительно|ориентировочно|примерно} 30000 руб/месяц при {средней|обычной} посещаемости сайта 1000 мобильных пользователей в {день|сутки}.
    <br><br>
    Подробнее по ссылке !faq_link! {(Это Google документ) | (Это Гугл документ)}
                        '

                        ), ['class'=>'form-control', 'style' => 'font-family: sans-serif;']

                        ) !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        Осталось
                        <span title="Осталось" id="count"></span>

                        <label class="form-check-label hide">
                            {!! Form::checkbox('skip', 'skip', false, ['class' => 'form-check-input']) !!}
                            Холостой прогон
                        </label>
                        <span style="margin-left: 20px;"></span>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::button('Следующий домен', ['class'=>'btn btn-primary btn-main main-button']) !!}
                    <span id="counter" style="margin-left: 20px;"></span>
                    <div id="tooltip" class="pull-right"></div>
                    {!! Form::button('Сохранить шаблон', ['class'=>'btn save-button pull-right', 'id'=>'save-button']) !!}

                </div>
                {!! Form::close()!!}

                <div id="preresult">
                    <div class="form-group text-center">
                        <p><div id="domain_link"></div></p>
                    </div>
                    <div class="form-group text-center action-buttons hide">
                        {!! Form::hidden('domain_id', null, ['id' => 'domain_id']) !!}
                        <span class="text-muted registrar"></span>

                    </div>
                </div>

                <div id="result">
                    <div id="link"></div>
                    <p><div id="domain"></div></p>
                    <p><div id="fio"></div></p>
                    <div id="email"></div>
                    <hr>
                    <h4></h4>
                    <p>
                        <div id="body"></div>
                        <div class="vert25"></div>
                        <div class="vert25"></div>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('js/random_manual_subdomain.js') }}"></script>

@endsection