<ul>
    <li>
        <a {{ (Request::is('financeiro')) ? 'class="on"' : '' }} href='{{ URL::to("financeiro") }}'>
            <span sclass='glyphicon glyphicon-home'></span> Página inicial
        </a>
    </li>
    <li>
        <a {{ (Request::is('financeiro/historico')) ? 'class="on"' : '' }} href='{{ URL::to("financeiro/historico") }}'>
            <span class='glyphicon glyphicon-dashboard'></span> Histórico de liberação
        </a>
    </li>
</ul>
