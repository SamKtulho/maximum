
<div class="form-group">
    {!! Form::label('content') !!}
    {!! Form::textarea('content', null, ['class'=>'form-control'] ) !!}
</div>

<div class="form-group">
    {!! Form::label('tic') !!}
    {!! Form::text('tic', null, ['class'=>'form-control'] ) !!}
</div>

<div class="form-group">
    {!! Form::submit('Submit', ['class'=>'btn btn-primary']) !!}
</div>