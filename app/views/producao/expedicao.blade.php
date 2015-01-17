@extends('layouts.default')

@section('title') <i class="glyphicon glyphicon-arrow-up"></i> Expedição @endsection

@section('content')

@if(count($entregas))
<div class='jtable'>
	<div data-section='header'>
		
	</div><!-- section="header" -->

	<table>
		<thead>
			<tr>
				<th>Nº da remessa</th>
				<th>Qtd. de cartões</th>
				<th>Responsável pela conferência</th>
				<th>Data da remessa</th>
				<th>Mais informações</th>
				<th>Liberação</th>
			</tr>
		</thead>
		<tbody>
			@foreach($entregas as $entrega)
			<tr>
				<td>{{ zero_fill($entrega->id, 4) }}</td>
				<td>{{ $entrega->solicitacoes->count() }}</td>
				<td>{{{ $entrega->responsavelStatus(10)->nome or '-' }}}</td>
				<td>{{ (new Datetime($entrega->data_criacao))->format('d/m/Y') }}</td>
				<td style='text-align:center'><a href='{{ URL::to("producao/info-remessa/$entrega->id/$currentPage") }}' class='wm-btn wm-btn-blue'><i class='glyphicon glyphicon-plus'></i> Informações</a></td>
				<td style='text-align:center'><button data-remessa='{{ $entrega->id }}' class='wm-btn wm-btn-green btn-liberar-remessa'><i class='glyphicon glyphicon-share-alt'></i> Sair para entrega</button></td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<div data-section='footer'>
		{{ $entregas->links('elements.paginate') }}
	</div><!-- section="footer" -->
</div><!-- .jtable -->
@else
<div class='j-alert-error'>Nenhuma remessa foi encontrada.</div>
@endif

@endsection

@include('elements.common-alert')

@section('scripts')
{{ HTML::script('js/wm-modal.js') }}
{{ HTML::script('js/producao/expedicao.js') }}
@endsection

@section('styles')
{{ HTML::style('css/wm-modal.css') }}
{{ HTML::style('css/jtable.css') }}
@endsection