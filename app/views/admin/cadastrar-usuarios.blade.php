@extends('layouts.defaultWt')

@section('title') Cadastrar usuários @stop

@section('content')

@if(isset($message))
<div class="j-alert-error">
	{{ $message }}
</div>
@endif

<form method="post" class="frm-cadastro">
	<input type="text" class="wm-input" name="username" id="username" placeholder="Usuário" required /><br>
	<input type="password" class="wm-input" name="password" id="password" placeholder="Senha" required /><br>
	<input type="text" required class="wm-input" name="nome" placeholder="Nome" /><br>
	<select name="nivel_id" class="wm-input" required>
		<option value="">Selecione o nível</option>
		@foreach($niveis as $nivel)
		<option value="{{ $nivel->id }}">{{ $nivel->titulo }}</option>
		@endforeach
	</select>
	<br>
	<button type="submit" class="wm-btn wm-btn-blue">Salvar</button>
</form>
@stop

@section('styles')
{{ HTML::style('css/admin/cadastrar-usuarios.css') }}
@stop