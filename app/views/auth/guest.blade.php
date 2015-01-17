@extends('layouts.login')

@section('content')

<div class="login">
	<div class="left">
		<img src="{{ URL::to('img/security.png') }}" />
	</div><!-- .left -->
	<div class="right">
		<h1>Acesso ao sistema</h1>
		<p class="error">{{ $errors->first('message') }}</p>
		<form method="post">
			<input autofocus type="text" name="username" class="input input-email" placeholder="Login" />
			<input type="password" name="password" class="input input-password" placeholder="Senha" />
			<button type="submit" class="btn-login">Entrar</button>
			<a href="#">Esqueci minha senha</a>
		</form>
	</div><!-- .right -->
</div><!-- .login -->

<div class="data-info">
	<h2><?=str_replace('-feira', '', strftime("%A, %d de %B"));?></h2>
	<h1><?=date('H:i')?></h1>
</div><!-- .data-info -->

@stop
