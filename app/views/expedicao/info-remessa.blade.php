@extends('layouts.default') @section('title') Remessa {{ $remessa }}
@endsection @section('content')

<div class='jtable'>
	<div data-section='header'>
		@if($solicitacoes[0]->status_atual == 5)
		<a href='{{ URL::to("expedicao/historico/?page=" . $pageBack) }}' class='wm-btn'>Voltar</a>
		@else
		<a href='{{ URL::to("expedicao/?page=" . $pageBack) }}' class='wm-btn'>Voltar</a>
		@endif
	</div>
	<!-- section="header" -->

	<table>
		<thead>
			<tr>
				<th colspan="2">Aluno</th>
				<th>Matrícula</th>
				<th>CPF</th>
				<th>Solicitado em</th>
			</tr>
		</thead>
		<tbody>
			@foreach($solicitacoes as $solicitacao)
			<tr>
				<td style="width: 45px"><img
					src='{{ URL::to("imagens/" . $solicitacao->usuario->matricula . "/" . $solicitacao->foto) }}'
					width='48' height='70'></td>
				<td>{{ $solicitacao->usuario->nome }}</td>
				<td style="text-align: center">{{ $solicitacao->usuario->matricula
					}}</td>
				<td style="text-align: center">{{ $solicitacao->usuario->cpf }}</td>
				<td style="text-align: center">{{ (new
					Datetime($solicitacao->created_at))->format('d/m/Y à\s H:i:s') }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<div data-section='footer'>{{ $solicitacoes->links('elements.paginate')
		}}</div>
	<!-- section="footer" -->
</div>
<!-- .jtable -->

@endsection @section('styles') {{ HTML::style('css/jtable.css') }}
@endsection
