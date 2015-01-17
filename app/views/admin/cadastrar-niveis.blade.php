@extends('layouts.default')

@section('title') Cadastrar níveis @stop

@section('content')

@if(isset($message))
<div class="j-alert-error">{{ $message }}</div>
@endif

<form method="post">
	<input type="text" name="titulo" id="titulo" class="wm-input" placeholder="Título do nível" />
	<button type="submit" class="wm-btn wm-btn-blue">Cadastrar</button>
</form>

@stop