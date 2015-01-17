@extends('layouts.defaultWt')

@section('title') Permissões do  nível {{ $nivel->titulo }} @stop

@section('content')

@if(isset($message))
	<div class="j-alert-error">{{ $message }}</div>
@endif

<form method="post" class="permissao-form">
	<label>O nível {{ $nivel->titulo }} deverá acessar:</label>
	<div class="search-group">
		<input required type="text" name="permissao_search" id="permissao-search" class="wm-input" />
		<div class="box-search-action">
			<!-- <div class="item">Controller@method</div> -->
		</div><!-- .box-search-action -->
	</div>
	<input type="hidden" name="permissao_id" id="permissao-id" />
	<!-- <select class="wm-input" required name="permissao_id">
		<option value="">-</option>
		@foreach($permissoesAll as $permissao)
			<option value="{{ $permissao->id }}">{{ "{$permissao->name} ({$permissao->type}) - {$permissao->url}" }}</option>
		@endforeach
	</select> -->
	<button type="submit" class="wm-btn wm-btn-blue">Salvar</button>
</form>

@if($permissoes->count())
<div class="jtable" style="margin-top:20px">
	<table>
		<thead>
			<tr>
				<th>Nome</th>
				<th>Ação</th>
				<th>Tipo</th>
				<th>URL</th>
				<th>Glyphicon</th>
				<th>No menu</th>
				<th colspan="2">Ações</th>
			</tr>
		</thead>
		<tbody>
			@foreach($permissoes as $permissao)
			<tr>
				<td>{{ $permissao->name }}</td>
				<td>{{ $permissao->action }}</td>
				<td>{{ $permissao->type }}</td>
				<td>{{ $permissao->url }}</td>
				<td>{{ $permissao->glyphicon }}</td>
				<td style="text-align:center"><i class="glyphicon glyphicon-{{ ($permissao->in_menu) ? 'ok' : 'minus'}}"></i></td>
				<td style="text-align:center">
					<form method="post">
						<input type="hidden" name="permissao_id_del" value="{{ $permissao->id }}" />
						<button type="submit" class="wm-btn wm-btn-red"><i class="glyphicon glyphicon-trash"></i></button>
					</form>
				</td>
				<td style="text-align:center">
					<form method="post">
						<input type="hidden" name="permissao_define_home_id" value="{{ $permissao->id }}" />
						@if($paginaInicialId == $permissao->id)
							<button type="button" class="wm-btn wm-btn-blue">
								<i class="glyphicon glyphicon-home"></i>
							</button>
						@else
							<button type="submit" title="Definir como página inicial" class="wm-btn">
								<i class="glyphicon glyphicon-home"></i>
							</button>
						@endif
					</form>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div><!-- .jtable -->
@else
<div class="j-alert-error">O nível {{ $nivel->titulo }} ainda não tem nenhuma permissão.</div>
@endif

@stop

@section('styles')
{{ 
	HTML::style('css/jtable.css'),
	HTML::style('css/admin/permissoes.css')
}}
@stop

@section('scripts')
{{
	HTML::script('js/admin/permissoes.js')
}}
@stop