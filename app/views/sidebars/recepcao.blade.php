<ul>

    <li>
        <a {{ (Request::is('producao/gerenciar-fotos')) ? 'class="on"' : '' }} href='{{ URL::to("producao/gerenciar-fotos") }}'>
           <span class="glyphicon glyphicon-film"></span>
           Gerenciar Fotos
        </a>
    </li>

    <li>
        <a {{ (Request::is('producao/baixar-carga')) ? 'class="on"' : '' }} href="{{ URL::to('producao/baixar-carga') }}">
            <span class="glyphicon glyphicon-download-alt"></span>
            Baixar Carga
        </a>
    </li>
    <li>
        <a {{ (Request::is('expedicao')) ? 'class="on"' : '' }} href="{{ URL::to('expedicao') }}">
            <span class='glyphicon glyphicon-arrow-up'></span>
            Expedição
        </a>
    </li>
    <li>
        <a {{ (Request::is('expedicao/historico')) ? 'class="on"' : '' }} href="{{ URL::to('expedicao/historico') }}">
            <span class='glyphicon glyphicon-dashboard'></span>
            Histórico de expedição
        </a>
    </li>
</ul>