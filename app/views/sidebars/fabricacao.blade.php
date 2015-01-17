<ul>
	<li>
		<a {{ (Request::is('producao')) ? 'class="on"' : '' }} href='{{ URL::to("producao") }}'> 
			<span class="glyphicon glyphicon-home"></span> Página inicial
		</a>
	</li>
</ul>