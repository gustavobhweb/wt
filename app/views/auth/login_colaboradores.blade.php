@extends('layouts.login')

@section('content')

<div class='body-login'>
	<h1>Seja bem vindo ao sistema web</h1>
	<h5>
		Para acessar sua conta digite no campo abaixo o seu <strong>CPF</strong>
		e <strong>senha</strong>.
	</h5>

	<div class='login-box'>
		<div class='errorLogin'>{{ $errors->first('message') }}</div>

		<form method='post'
			style='width: 385px; float: left; margin: 0 0 0 25px'>
			<p>CPF</p>
			{{
				Form::text(
					'cpf',
					Input::old('cpf'),
					[
						'id'       => 'txt-input-login',
						'required' => 'required'
					]
				) 
			}}

			<p style='float: left; width: 100%; margin: 10px 0 0 0'>Senha</p>

			{{ 
				Form::password(
					'senha',
					[
						'id' => 'text-input-password',
						'required' => 'required'
					]
				) 
			}}

			<button class='btn-login' type='submit'>CONTINUAR ></button>

			<h5
				style='float: left; width: auto; text-align: left; margin: 7px 0 0 0'>
				<input type='checkbox' name='ckb_manter' /> Mantenha-me conectado
			</h5>
		</form>
		<div class='clear'></div>
	</div>
	<!--login-box-->
</div>
<!--body-login-->

@stop

@section('scripts')

{{ HTML::script('js/login_colaboradores.js') }}

@stop