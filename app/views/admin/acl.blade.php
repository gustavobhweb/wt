@extends('layouts.defaultWt')

@section('title') ACL @stop

@section('topbar')
	<h4><i class="glyphicon glyphicon-lock"></i> Access Control List</h4>
	<button class="btn medium orange btn-gerenciar dropdown-toggle"><i class="glyphicon glyphicon-cog"></i> Gerenciar <span class="caret"></span></button>
@endsection

@section('content')

	<div class="charts">
		<div class="pieChart"></div>
		<div class="barsChart"></div>
	</div><!-- .charts -->

	@if(isset($message))
		<div class="j-alert-error">
			{{ $message }}
		</div>
	@endif
	
	<form method="post" style="martin">
		<label>O nível</label>
		<select class="wm-input" required name="nivel_id">
			<option value="">-</option>
			@foreach($niveis as $nivel)
			<option value="{{ $nivel->id }}">{{ $nivel->titulo }}</option>
			@endforeach
		</select>
		<label>deverá acessar</label>
		<select class="wm-input" required name="permissao_id">
			<option value="">-</option>
			@foreach($permissoes as $permissao)
			<option value="{{ $permissao->id }}">{{ "{$permissao->name} ({$permissao->type})" }}</option>
			@endforeach
		</select>
		<button type="submit" class="btn green medium"><i class="glyphicon glyphicon-ok"></i> Salvar</button>
	</form>

	<div class="jtable" style="margin:20px 0 0 0">
		<table>
			<thead>
				<tr>
					<th>Nível</th>
					<th>Qtd. permissões</th>
					<th>Qtd. de usuários</th>
					<th>Ações</th>
				</tr>
			</thead>
			<tbody>
				@foreach($niveis as $nivel)
				<tr>
					<td>{{ $nivel->titulo }}</td>
					<td style="text-align:center">{{ $nivel->permissoes->count() }}</td>
					<td style="text-align:center">{{ Usuario::whereNivelId($nivel->id)->count() }}</td>
					<td style="text-align:center">
						<a href="{{ URL::to('admin/permissoes/' . $nivel->id) }}" class="btn orange"><i class="glyphicon glyphicon-edit"></i> Permissões</a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div><!-- .jtable -->

@stop

@section('styles')
{{ 
	HTML::style('css/admin/acl.css'),
	HTML::style('css/jtable.css') 
}}
@stop

@section('scripts')
{{ 
	HTML::script('js/globalize.min.js'),
	HTML::script('js/dx.chartjs.js'),
	HTML::script('js/admin/acl.js')
}}
@stop