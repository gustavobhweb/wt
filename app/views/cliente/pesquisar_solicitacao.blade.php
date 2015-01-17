@extends('layouts.default')

@section('title') Histórico de solicitação @stop

@section('content')

<form id='buscar-aluno' method="POST" autocomplete="off">
	<input type='text' name='matricula' placeholder='Digite o RA do aluno' class='wm-input input-large' autofocus />
	<button type='submit' class='wm-btn wm-btn-blue'>Buscar Aluno</button>
</form>

@if(isset($error))
	<div class='j-alert-error'>
		{{ $error }}
	</div>
@endif

@if(isset($solicitacoes))
	<?php $via = count($solicitacoes);?>
	@foreach($solicitacoes as $solicitacao)
		@if ($via == count($solicitacoes) - 1)
			@if (count($solicitacoes) > 2)
				<h2 class='historico-title'>Histórico de solicitações</h2>
			@elseif(count($solicitacoes) > 1)
				<h2 class='historico-title'>Histórico de solicitação</h2>
			@endif
		@endif

		<div class='situacao-box'>
			
			@if($solicitacao->status->id == 8)
			<div class='reprovada'>
				<h1>FOTO REPROVADA</h1>
			</div>
			@endif

			<h2 style="margin-bottom: 10px">Situação</h2>
			<div class='left-infos'></div>
			<div class='infos'>
				<div style="float: left; width: 75%">
					<h3 class='via'>{{ $via }} VIA(S)</h3>
					<div class='modelos'>
						<div class='modelo frente'>
							<img width='71' height='95'
							src='{{ URL::to("imagens/" . $usuario->matricula . "/" . $solicitacao->foto) }}' />
						</div>
						<!-- .frente -->
						<div class='modelo verso'></div>
					</div>
					<!-- .modelos -->
					<div class='regua'>
						<div
							class='passo {{ ($solicitacao->status_atual >= 2 && $solicitacao->status_atual != 8) ? "orange" : "" }}'
							data-step='1'>
							<p>Análise da foto</p>
						</div>
						<div
							class='passo {{ ($solicitacao->status_atual >= 3 && $solicitacao->status_atual != 8) ? "steelblue" : "" }}'
							data-step='2'>
							<p>Fabricação</p>
						</div>
						<div
							class='passo {{ ($solicitacao->status_atual >= 4 && $solicitacao->status_atual != 8 && $solicitacao->status_atual != 9) ? "blue" : "" }}'
							data-step='3'>
							<p>Conferência</p>
						</div>
						<div
							class='passo {{ ($solicitacao->status_atual >= 6 && $solicitacao->status_atual != 8 && $solicitacao->status_atual != 9 && $solicitacao->status_atual != 10) ? "steelgreen" : "" }}'
							data-step='4'>
							<p>Disponível para entrega</p>
						</div>
						<div
							class='passo {{ ($solicitacao->status_atual >= 7 && $solicitacao->status_atual != 8 && $solicitacao->status_atual != 9 && $solicitacao->status_atual != 10) ? "green" : "" }}'
							data-step='5'>
							<p>Entregue</p>
						</div>
					</div>
					<!-- .regua -->
				</div>
				<div class="status-list">
					<div class='title'></div>
					<div class='content'>

						@foreach($solicitacao->solicitacoesStatus as $key => $val)
						@if($solicitacao->solicitacoesStatus[$key]->status->id != 9)
						<div class='item'>
							<h3>{{ $letters[$key] }}</h3>
							<div class='right'>
								<h4>{{ $solicitacao->solicitacoesStatus[$key]->status->titulo }}</h4>
								<p>{{ date('d/m/Y à\s H:i',
								strtotime($solicitacao->solicitacoesStatus[$key]->created_at)) }}</p>
								
								@if($solicitacao->status_atual > 2 && $key == 0)
								<div class='add'>
									<h4>Aprovado</h4>
									<p>{{ date('d/m/Y à\s H:i',
									strtotime($solicitacao->solicitacoesStatus[$key +
									1]->created_at)) }}</p>
								</div>
								
								@endif
							</div>
							<!-- .right -->
						</div>
						<!-- .item -->
						@endif
						@endforeach
					</div>
					<!-- .content -->
				</div>
				<!-- .status-list -->
			</div>
			<!-- .infos -->
		</div><!-- .situacao-box -->
	<?php $via--;?>
	@endforeach
@endif

@include('elements.common-alert')

@stop

@section('styles')
	{{ HTML::style('css/cliente/pesquisar_solicitacao.css'),
	   HTML::style('css/wm-modal.css') }}
@stop

@section('scripts')
{{ 
	HTML::script('js/jquery-ui.js'),
	HTML::script('js/wm-modal.js'),
	HTML::script('js/cliente/pesquisar_solicitacao.js')
}}
@stop