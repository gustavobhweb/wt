<!DOCTYPE html>
<html>
<head>
	<title>WorkTab</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0, user-scalable=no">
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
	<link rel="icon" type="image/png" href="{{ URL::to('img/favicon.png') }}" />
	{{ HTML::style('css/glyphicons.css') }}
	{{ HTML::style('css/default_wt.css') }}
	{{ HTML::style('css/jtable.css') }}
	@yield('styles')

	{{ HTML::script('js/jquery-1.11.0.min.js') }}
	{{ HTML::script('js/default.js') }}
	@yield('scripts')
</head>
<body>

	<div class="header">
		<div class="main">
			<a href="{{ URL::to('/') }}"><img src="{{ URL::to('img/worktab.png') }}" /></a>
			<button id="btn-menu-header"></button>
		</div><!-- .main -->
	</div><!-- .header -->

	<div data-open="false" id="right-top-menu">
		<ul>
			<li><a href="#"><i class="glyphicon glyphicon-user"></i> Minha conta</a></li>
			<li><a href="{{ URL::to('logout') }}"><i class="glyphicon glyphicon-remove"></i> Sair</a></li>
		</ul>
	</div><!-- .right-top-menu -->

	<div class="menu">
		<div class="top">
			<div class="main">
				<img onerror="$(this).attr('src', '{{ URL::to('img/defaultUser.png') }}')" src="#" />
				<h5>Seja bem-vindo(a),</h5>
				<p>{{ Auth::user()->nome }}</p>
				<div class="clear"></div>
			</div><!-- .main -->
		</div><!-- .top -->
		<form class="form-search">
			<input type="text" placeholder="Pesquisar" id="input-search" />
			<button type="button" id="button-search"><i class="glyphicon glyphicon-search"></i></button>
			<div class="clear"></div>
		</form>
		<ul>
		    @foreach($user->nivel->permissoes as $permissao)
		    	@if($permissao->in_menu)
			    <li>
			        <a href='{{ URL::to($permissao->url) }}' {{ (Request::is($permissao->url)) ? 'class="on"' : '' }} >
			            <span class="glyphicon glyphicon-{{ $permissao->glyphicon }}"></span>
			            {{ $permissao->name }}
			        </a>
			    </li>
			    @endif
		    @endforeach
		</ul>
	</div><!-- .menu -->

	@if(Session::has('submenu') && count(Session::get('submenu')))
	<div class="submenu">
		<ul>
			@foreach(Session::get('submenu') as $k => $v)
				@if(Request::is($v['newUrl']))
				<li class="on" data-id="$k">
					<a href="{{ $v['url'] }}">{{ $v['name'] }}</a>
					<a class="close" href="{{ URL::to('close-tab/' . $k) }}"><i class="glyphicon glyphicon-remove"></i>
				</a>
				</li>
				@else
				<li data-id="$k">
					<a href="{{ $v['url'] }}">{{ $v['name'] }}</a>
					<a class="close" href="{{ URL::to('close-tab/' . $k) }}"><i class="glyphicon glyphicon-remove"></i></a>
				</li>
				@endif
			@endforeach
		</ul>
	</div><!-- .submenu -->
	@endif

	<div class="topbar">
		@yield('topbar')
		<div class="clear"></div>
	</div><!-- .topbar -->

	<div class="content">
		@yield('content')
	</div><!-- .content -->

</body>
</html>