
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
                    {!! Form::text('title', (!empty($title) ? $title : 'У вас {1|} сообщение владельцу домена !dom_link!'), ['class'=>'form-control'] ) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('Текст') !!}
                    {!! Form::textarea('content', (!empty($content) ? $content :
                    '
{Здравствуйте.|Добрый день.|Приветствую вас.}
<br><br>
{Если не ошибаюсь вы являетесь владельцем домена|Мне вас порекомендовали как владельца домена} !dom_link!
<br><br>
Я представляю компанию WapMaximum - международную партнерскую программу по монетизации мобильного трафика по средствам технологий 1Click, PIN-submit.
<br>
Предлагаем вам сотрудничество и стабильный заработок по проекту... !faq_link!

                    '

                    ), ['class'=>'form-control']

                    ) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Submit', ['class'=>'btn btn-primary']) !!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>
    </div>
@endsection