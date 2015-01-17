<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		*{
			margin:0;
			padding:0;
			box-sizing: border-box;
		}
		@page{
			border:1px solid #333;
			margin:0;
			size:a4 portrait;
			/*size:a4 landscape;*/
		}
		.page{
			page-break-after: always;
		}
		.page:last-of-type{
			page-break-after: auto;	
		}
	</style>
	{{ HTML::style('css/jtable.css'); }}
</head>
<body>
	<div class="page">
		<div class="jtable">
			<table>
				<thead>
					<tr>
						<th>Aluno</th>
						<th>Matrícula</th>
						<th>Código W</th>
						<th>Curso</th>
						<th>Turno</th>
					</tr>
				</thead>
				<tbody>
					@foreach($solicitacoes as $solicitacao)
					<tr>
						<td>{{ $solicitacao->usuario->nome }}</td>
						<td>{{ $solicitacao->usuario->matricula }}</td>
						<td>{{ $solicitacao->codigo_w }}</td>
						<td>{{ $solicitacao->usuario->curso }}</td>
						<td>{{ $solicitacao->usuario->turno }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div><!-- .jtable -->
	</div><!-- .page -->
</body>
</html>