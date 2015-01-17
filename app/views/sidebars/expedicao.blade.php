<ul>

    <li>
        <a {{ (Request::is('expedicao')) ? 'class="on"' : '' }} href='{{ URL::to("expedicao") }}'>
            <span class="glyphicon glyphicon-home"></span>
            Página inicial
        </a>
    </li>

    <li>
        <a {{ (Request::is('expedicao/historico')) ? 'class="on"' : '' }} href='{{ URL::to("expedicao/historico") }}'>
            <span class="glyphicon glyphicon-dashboard"></span>
            Histórico de saída
        </a>
    </li>

</ul>