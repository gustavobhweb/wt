@extends('layouts.defaultWt')

@section('title') <i class="glyphicon glyphicon-user"></i> Controle de usuários @stop

@section('content')
	<div style="width:100%">
		<a href="{{ URL::to('admin/cadastrar-usuarios') }}" class="btn medium blue"><i class="glyphicon glyphicon-plus"></i> Cadastrar novo usuário</a>
		<a href="{{ URL::to('admin/gerenciar-niveis') }}" class="wm-btn wm-btn-blue"><i class="glyphicon glyphicon-tower"></i> Gerenciar níveis</a>
	</div>
	@foreach($niveis as $nivel)
	<div class="section-nivel" data-id="{{ $nivel->id }}">
		<div class="title">
			Nível {{ $nivel->titulo }} - {{ $nivel->usuarios->count() }} usuário(s)
		</div><!-- .title -->
		<div class="users">
		@if($nivel->usuarios->count())
			@foreach($nivel->usuarios as $usuario)
			<div class="user" data-id="{{ $usuario->id }}">
				<i class="glyphicon glyphicon-user"></i> {{{ $usuario->nome }}}
			</div><!-- .user -->
			@endforeach
		@else
			<!-- <button class="wm-btn wm-btn-red" style="margin:5px 0 0 0;width:100%"><i class="glyphicon glyphicon-trash"></i> Deletar nível</button> -->
		@endif
		</div><!-- .users -->
	</div><!-- .section-nivel -->
	@endforeach
@stop

@section('styles')
	{{ HTML::style('css/admin/controle-de-usuarios.css') }}
@stop

@section('scripts')
	{{ 
		HTML::script('js/jquery-ui.js'),
		HTML::script('js/admin/controle-de-usuarios.js')
	}}
@stop