
<div class="form-group">
    {!! Form::label('content') !!} (добавлять в формате example.com mail@mail.ru,mail2@gamil.com)
    {!! Form::textarea('content', null, ['class'=>'form-control', 'placeholder' => "example.com mail@mail.ru,mail2@gamil.com\ndomen2.com vasya@pupkin.net"] ) !!}
</div>

<div class="form-group">
    {!! Form::label('tic') !!} (указать ТИЦ домена)
    {!! Form::text('tic', null, ['class'=>'form-control'] ) !!}
</div>

<div class="form-group">
    {!! Form::submit('Submit', ['class'=>'btn btn-primary']) !!}
</div>