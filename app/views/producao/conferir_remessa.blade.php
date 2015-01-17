@extends('layouts.default') @section('title') Conferência da remessa
@stop @section('content') @if($count = $solicitacoes->count())
<h3 class='pull-right'>Remessa {{ zero_fill($remessa->id, 4) }}</h3>
<h3>Número de solicitações: {{ zero_fill($count, 2) }}</h3>
<table class="wm-table big">
	<thead>
		<tr>
			<th></th>
			<th>Foto</th>
			<th>Nome</th>
			<th>Matricula</th>
			<th>Código W</th>
			<th>Curso</th>
		</tr>
	</thead>
	<tbody>
		@foreach($solicitacoes as $solicitacao)
		<tr>
			<td>@if($solicitacao->status_atual == 10) <span
				class="status-indicator ok glyphicon glyphicon-ok-sign"></span>
				@else <span
				class="status-indicator waiting glyphicon glyphicon-minus"></span>
				@endif
			</td>

			<td><img title="Solicitação: {{ zero_fill($solicitacao->id, 4) }}"
				width="50" height="70"
				src="{{ URL::to('imagens', [$solicitacao->usuario->matricula, $solicitacao->foto]) }}" />
			</td>
			<td>{{ $solicitacao->usuario->nome }}</td>
			<td>{{ $solicitacao->usuario->matricula }}</td>
			<td>{{ $solicitacao->codigo_w }}</td>
			<td>{{ $solicitacao->usuario->curso }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
@else
<div class="j-alert-error">Não há solicitações para produção</div>
@endif

<section>
	<div class="clearfix">
		{{ Form::open(['files' => true]) }}
		<div class="wm-input-container">
			<div class="warning">
				<p>
					Selecione o seu arquivo <strong>XLS</strong> ou <strong>XLSX</strong>
					contendo as colunas <strong>matricula</strong> e <strong>codigo_w</strong>
					e clique no botão <strong>Enviar</strong>
				</p>
				<p>
					Os arquivos deverão ter todos os campos de acordo com o <a
						target="_blank" class="link"
						href="{{ URL::to('xls/modelo/conferencia.xls') }}">modelo</a>
				</p>
			</div>

			<div class='text-right'>{{ Form::file('excel', ['style' =>
				'display:none', 'id' => 'hidden-excel-file-input']) }} {{
				Form::button( 'Selecione um arquivo ...', [ 'id' =>
				'fake-file-excel', 'class' => 'wm-btn', ] ) }} {{
				Form::button('Enviar', ['class' => 'wm-btn', 'type' => 'submit']) }}
			</div>
		</div>


		@if($errors->has('message'))
		<div class="j-alert-error">{{ $errors->first('message') }}</div>
		@endif @if(Session::has('successMessage')) {{
		Session::get('successMessage') }} @endif {{ Form::close() }}
	</div>
</section>
@stop @section('scripts') {{
HTML::script('js/producao/conferir_remessa.js') }} @stop


@section('styles') {{ HTML::style('css/producao/conferir_remessa.css')
}} @stop
