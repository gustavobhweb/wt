@extends('layouts.defaultWt')

@section('content')
<form method="post" id="frm-novo-nivel">
	<input type="text" name="nome-nivel" placeholder="Nome do nível" required />
	<button class="btn blue medium">Cadastrar</button>
</form><br>

<div class="jtable">
	<table>
		<thead>
			<tr>
				<th>Nível</th>
				<th>Qtd. de permissões</th>
				<th>Qtd. de usuários</th>
				<th>Ações</th>
			</tr>
		</thead>
		<tbody>
			@foreach($niveis as $nivel)
			<tr>
				<td>{{ $nivel->titulo }}</td>
				<td class="center">{{ $nivel->permissoes->count() }}</td>
				<td class="center">{{ $nivel->usuarios->count() }}</td>
				<td class="center"><button class="btn blue"><i class="glyphicon glyphicon-edit"></i></button></td>
			</tr>
			@endforeach
		</tbody>
	</table>
</table><!-- .jtable -->
@stop