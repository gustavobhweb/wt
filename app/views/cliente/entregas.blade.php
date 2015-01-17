@extends('layouts.default')


@section('title') Entregas @stop

@section('content')

@if(count($entregas))
<div class='jtable'>
	<table>
		<thead>
			<tr>
				<th>Nº da remessa</th>
				<th>Qtd. de cartões</th>
				<th>Responsável pelo envio</th>
				<th>Data da remessa</th>
				<th>Conferir</th>
			</tr>
		</thead>
		<tbody>
			@foreach($entregas as $remessa)
			<tr>
				<td>{{ zero_fill($remessa->id, 4) }}</td>
				<td>{{ $remessa->solicitacoes->count() }}</td>
				<td style='text-align:center'>{{{ $remessa->responsavelStatus(3)->nome or '-' }}}</td>
				<td>{{ (new Datetime($remessa->data_criacao))->format('d/m/Y') }}</td>
				<td style='text-align:center'><a href="{{ URL::to('cliente/conferir/' . $currentPage . '/' . $remessa->id) }}" data-remessa='{{ $remessa->id }}' class='wm-btn wm-btn-green'><i class='glyphicon glyphicon-ok'></i> Conferir remessa</a></td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<div data-section='footer'>
		{{ $entregas->links('elements.paginate') }}
	</div><!-- section="footer" -->
</div><!-- .jtable -->
@else
    <div class='j-alert-error'>Nenhuma remessa está sendo entregue.</div>
@endif 

@include('elements/common-alert')

@stop 


@section('styles')
    {{ 
        HTML::style('css/jtable.css'),
        HTML::style('css/wm-modal.css') 
    }} 
    
@stop


@section('scripts') 

    {{ 
        HTML::script('js/wm-modal.js'), 
        HTML::script('js/cliente/entregas.js')
    }}
    
@stop
