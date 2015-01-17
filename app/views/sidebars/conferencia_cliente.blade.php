<ul>
	<li>
        <a href='{{ URL::to("cliente/entregas") }}' {{ (Request::is('cliente/entregas')) ? 'class="on"' : '' }} >
            <span class="glyphicon glyphicon-briefcase"></span>
            Conferir recebimento
        </a>
    </li>
    <li>
        <a href='{{ URL::to("cliente/retirada") }}' {{ (Request::is( 'cliente/retirada')) ? 'class="on"' : '' }}>
            <span class="glyphicon glyphicon-arrow-up"></span> Retirada
        </a>
    </li>
    <li>
        <a href='{{ URL::to("cliente/pesquisar-solicitacao") }}' {{ (Request::is( 'cliente/pesquisar-solicitacao')) ? 'class="on"' : '' }}>
            <span class="glyphicon glyphicon-dashboard"></span> Histórico da solicitação
        </a>
    </li>
</ul>