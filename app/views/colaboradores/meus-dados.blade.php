@extends('layouts.default')

@section('title') Meus dados @stop

@section('content')

<div class="conteudo-form" style='width: 100%'>

	{{ Form::model($aluno, ['id' => 'form-meus-dados']) }}

	<div class='clearfix'>
		{{ $errors->first('message', '
		<div class="j-alert-error">:message</div>
		') }}
	</div>


	<br />

	<fieldset>
		<h5>E-mail principal:</h5>
		<div class="icon-status emailPrincipal">
			<span>E-mail:</span> 

			{{ 
				Form::email(
					'email', 
					Input::old('email'),
					[
						'placelholder' => 'nome@dominio.com',
						'id' => 'email',
						'title' =>'Informe um e-mail válido'
					]
				) 
			}}

			<div class="glif">
				<div class="glyphicon glifEmail glyphicon-ok"></div>
			</div>
		</div>
		<div class="icon-status emailPrincipal">
			<span>Confirmação e-mail:</span> {{ Form::email( 'confirmaEmail',
			Input::old('confirmaEmail'), [ 'placelholder' => 'nome@dominio.com',
			'id' => 'confirmaEmail', 'title' => 'e-mail não confere' ] ) }}

			<div class="glif">
				<div class="glyphicon glifEmail glyphicon-ok"></div>
			</div>


		</div>
		<label id="infoEmailPrincipal">
			<p class="text-center">Obs.:Digite seu e-mail com atenção, caso
				ocorra algum problema após envio dos dados você receberá um
				comunicado.</p>
		</label>

		<h5 id="titulo-endereco">Endereço residencial:</h5>
		<div class="icon-status dadosEndereco cep">
			<span>Cep:</span> {{ Form::text( 'cep', Input::old('cep'), [ 'id' =>
			'cep', 'class' => 'cepInput', 'placeholder' => '00000-000', 'pattern'
			=> '\d{5}-\d{3}' ] ) }}

			<button type="button"
				class="btn-conf-iza btn-verificar-cep pull-left"
				style="height: 47px">VERIFICAR</button>
			<a href='http://www.buscacep.correios.com.br/' target='_blank'> <span
				class="btn_nao_sabe">Não sei meu cep</span>
			</a>

			<div class="j-alert-error pull-left" style="display: none"
				id="cep-error">Cep não encontrado</div>

		</div>
		<div>
			<div class="icon-status dadosEndereco uf">
				<span>UF:</span>
				<div class="glif">
					<div class="glyphicon glifSelect glyphicon-ok"></div>
				</div>

				{{ Form::select( 'uf', $estado, false, ['id' => 'uf', 'required' =>
				'required'] ) }}
			</div>
			<div class="icon-status dadosEndereco cidade">
				<span>Cidade:</span>
				<div class="glif">
					<div class="glyphicon glifSelect glyphicon-ok"
						style="margin-left: 207px !important;"></div>
				</div>


				{{ Form::select( 'cidade', $cidade, $user->cidade, ['id' =>
				'cidade', 'required' => 'required'] ) }}
			</div>
			<div class="icon-status dadosEndereco endereco">
				<span>Endereço:</span> {{ Form::text( 'endereco',
				Input::old('endereco'), ['required' => 'required', 'placeholder' =>
				'Endereço', 'id' => 'endereco'] ) }}

				<div class="glif">
					<div class="glyphicon glyphicon-ok"></div>
				</div>
			</div>
			<div class="icon-status dadosEndereco numero">
				<span>Número:</span> {{ Form::text( 'numero', Input::old('numero'),
				[ 'required' => 'required', 'placeholder' => 'Número', 'id' =>
				'numero' ] ) }}
			</div>
		</div>
		<div style="clear: both;"></div>
		<div class="icon-status dadosEndereco bairro">
			<span>Bairro:</span> {{ Form::text( 'bairro', Input::old('bairro'), [
			'pattern' => '.{3,}', 'required' => 'required', 'id' => 'bairro',
			'placeholder' => 'Bairro' ] ) }}

			<div class="glif">
				<div class="glyphicon glyphicon-ok"></div>
			</div>

		</div>
		<div class="icon-status dadosEndereco complemento">
			<span>Complemento:</span> 
			{{
				Form::text(
					'complemento',
					Input::old('complemento'),
					[
						'id'          => 'complemento',
						'pattern'     => '.{2,}', 
						'placeholder' => 'Ex.: Bloco 5, Aptº 204...'
					]
				)
			}}
			<div class="glif">
				<div class="glyphicon glifComplemeno glyphicon-ok"></div>
			</div>

		</div>
	</fieldset>

	<div class="pull-right">{{ Form::reset( 'CANCELAR', ['style' =>
		'float:none', 'class' => 'btn-cancel-iza'] ) }} {{ Form::submit(
		'SALVAR', [ 'style' => 'float:none', 'name' => 'btn-submit', 'class'
		=> 'btn-conf-iza' ] ) }}</div>
	<!-- .captcha-wrap -->
	{{ Form::close() }}
</div>
<!-- .conteudo-form -->

@include('elements/common-alert') 

@stop 

@section('styles') 
{{
	HTML::style('css/enviar-foto.css'), 
	HTML::style('css/wm-modal.css'),
	HTML::style('css/aluno/meus_dados.css') 
}} 
@stop 

@section('scripts') 

{{
	HTML::script('js/wm-modal.js'), 
	HTML::script('js/jquery-ui.js'),
	HTML::script('js/aluno/meus_dados.js'),
	HTML::script('js/jQueryObjectForm.js') 
}}
@stop



