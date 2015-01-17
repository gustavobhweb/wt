<!DOCTYPE html>
<html>
    <head>

        {{ HTML::style('css/default.css') }}
         
        @yield('styles') 

        {{ HTML::script('js/jquery-1.10.2.min.js')  }}
         
        {{ HTML::script('js/jquery.mask.min.js')  }}
         
        {{ HTML::script('js/default.js') }}
         
        @yield('scripts')

        <link rel="shortcut icon" type="image/png" href="{{ URL::to('img/favicon.png?10') }}" />

        <title>Newton Paiva</title>
    </head>
<body>
	<div class='content-box'>

		<div class='left-menu-box'>
			<a href="{{ URL::to('/') }}"> 
                <img class='logo-tmt-box' src='{{ URL::to("img/newton.png") }}' width='200' />
			</a>
			<div style='width: 220px; float: left; margin: 30px 0 0 40px'>
				<h1 style='font: 22px Arial; color: #0865AE'>Olá, {{ $user->nome }}!</h1>
				<div class='line' style='margin: 10px 0 0 0'></div>
				<div class='menu'>
                    {{ $sidebar }} 
                    <img src='{{ URL::to("img/carteira_1.png")  }}' style='margin: 20px 0 0 0' width='220' />
				</div>
				<!--menu-->
			</div>
		</div>
		<!--left-menu-box-->

		<div class='main-content'>

			<nav class="top-menu">
				<a href='{{URL::current() }}'>
					<div class='solicsHovered'>
						<h1>@yield('title')</h1>
					</div>
				</a>

				<div class='menu-usuario'
					style="width: 480px; margin: 30px 30px 0 0">
					<div id="descricao-nome-curso">
						<div>{{ $user->nome }}</div>
                        @if(!empty($user->curso))
						<div style='font-size: 15px'>
                            Curso: {{ $user->curso }}
                        </div>
						@endif
					</div>

					<div style="width: 175px; float: right">
						<a style='text-decoration: none' href='{{ URL::to("/") }}'>
                            <div class='pagina-inicial'>
								<p>Página inicial</p>
								<img src='{{ URL::to("img/home-blue.png") }}' />
							</div>
                        </a>
						<div class='minha-conta'>
							<img src='{{ URL::to("img/arrow-down.png") }}' class='arrow-icon' />
							<p>{{ $user->nome }}</p>
						</div>
						<!--minha-conta-->

						<a class='sair-link' href='{{ URL::to("auth/meus-dados") }}'> 
                            <span style='margin: 0 0 0 4px; color: #069; font-size: 16px' class="glyphicon glyphicon-edit"></span> 
                            <span style='margin: 5px'>Meus dados</span>
						</a> 
                        <a class='sair-link' href='{{ URL::to("logout")  }}'> 
                            <span class="sair-icone"></span> <span style='margin: 5px'>Sair</span>
						</a>
					</div>
				</div>
				<!--menu-usuario-->
			</nav>
			<div class="clearfix"></div>
			<section class="container-section">@yield('content')</section>

		</div>
		<!--main-content-->

		<div class='clear'></div>

	</div>
	<!--content-box-->

	<div class='clearfix'></div>

	<div class='footer'>
		<div style='float: left; margin: 30px 0 0 15px; width: 330px'>
			<p style='font: 13px arial; color: #888888'>Termos de Uso | Política
				de Privacidade</p>
			<b style='font: 11px arial; color: #888888; font-weight: bold'>© 2014
				GrupoTMT.com.br. Todos os direitos reservados.</b>
		</div>
		<img src='{{ URL::to("img/footer-login.png") }}' />
		<a href="https://cardvantagens.com.br" target="_blank"><img src='{{ URL::to("img/cv.png") }}' width="200" style="margin:20px 15px 0 0" /></a>
		<div class='clearfix'></div>
	</div>
	<!--footer-->

</body>
</html>