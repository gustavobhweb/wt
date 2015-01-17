@extends('layouts.defaultWt')

@section('title') Cadastrar permissões @stop

@section('content')
@if(isset($message))
<div class="j-alert-error">
	{{ $message }}
</div>
@endif

<form method="post" class="frm-cadastro">
	<input type="text" id="debug" required class="wm-input" name="name" placeholder="Name" /><br>
	<div style="position:relative">
		<input type="text" autocomplete="off" required class="wm-input" id="input-action" name="action" placeholder="Action" /><br>
		<div class="box-search-action">
			<!-- <div class="item">Controller@method</div> -->
		</div><!-- .box-search-action -->
	</div>
	<input type="text" required class="wm-input" id="input-url" name="url" placeholder="URL" /><br>
	<input type="text" class="wm-input" name="glyphicon" placeholder="Glyphicon" /><br>
	<select id="select-type" class="wm-input" name="type" required>
		<option value="">Tipo da requisição</option>
		<option value="any">ANY</option>
		<option value="get">GET</option>
		<option value="post">POST</option>
		<option value="put">PUT</option>
		<option value="delete">DELETE</option>
	</select><br>
	<select name="in_menu" class="wm-input" required>
		<option value="">No menu</option>
		<option value="1">Sim</option>
		<option value="0">Não</option>
	</select>
	<br>
	<a href="{{ URL::to('admin/acl') }}" type="button" class="wm-btn">Cancelar</a>
	<button type="submit" class="wm-btn wm-btn-blue">Salvar</button>
</form>
@stop

@section('styles')
{{ HTML::style('css/admin/cadastrar-permissoes.css') }}
@stop

@section('scripts')
{{ HTML::script('js/admin/cadastrar-permissoes.js') }}
@stop