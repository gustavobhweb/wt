@extends('layouts.defaultWt')

@section('content')
<button class="btn blue medium"><i class="glyphicon glyphicon-plus"></i> Cadastrar ítem</button>
<div class="jtable" style="margin:10px 0 0 0">
	<table>
		<thead>
			<tr>
				<th>Título</th>
				<th>Aluno</th>
				<th>Data postagem</th>
				<th>Data de entrega</th>
				<th>Ações</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="center">Exercício 1 de LTP1</td>
				<td class="center">Gustavo Carmo</td>
				<td class="center">10/02/2015</td>
				<td class="center">16/02/2015</td>
				<td class="center">
					<button class="btn green"><i class="glyphicon glyphicon-file"></i></button>
					<button class="btn blue"><i class="glyphicon glyphicon-plus"</button>
				</td>
			</tr>
		</tbody>
	</table>
</div>
@stop