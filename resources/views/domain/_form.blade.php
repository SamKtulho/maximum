
<div class="form-group">
    {!! Form::label('content') !!} (добавлять в формате example.com)
    {!! Form::textarea('content', null, ['class'=>'form-control', 'placeholder' => "example.com \ndomen2.com"] ) !!}
</div>

<div class="form-group">
    {!! Form::label('source') !!} (указать источник доменов)
    {!! Form::text('source', null, ['class'=>'form-control', 'placeholder' => 'Serp mp3'] ) !!}
</div>

<div class="form-group">
    {!! Form::submit('Submit', ['class'=>'btn btn-primary']) !!}
</div>