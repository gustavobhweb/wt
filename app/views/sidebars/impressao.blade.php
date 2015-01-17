<ul>
    <li>
        <a {{ (Request::is('producao/baixar-carga')) ? 'class="on"' : '' }} href="{{ URL::to('producao/baixar-carga') }}">
            <span class="glyphicon glyphicon-download-alt"></span>
            Baixar Carga
        </a>
    </li>
    <li>
        <a {{ (Request::is('producao/lista-remessas-conferencia')) ? 'class="on"' : '' }} href="{{ URL::to('producao/lista-remessas-conferencia') }}">
            <span class='glyphicon glyphicon-barcode'></span>
            Conferência
        </a>
    </li>
    <li>
        <a {{ (Request::is('producao/protocolos')) ? 'class="on"' : '' }} href="{{ URL::to('producao/protocolos') }}">
            <span class='glyphicon glyphicon-print'></span>
            Imprimir protocolos
        </a>
    </li>
    <li>
        <a {{ (Request::is('producao/pesquisar-remessas')) ? 'class="on"' : '' }} href="{{ URL::to('producao/pesquisar-remessas') }}">
            <span class='glyphicon glyphicon-search'></span>
            Pesquisar remessas
        </a>
    </li>
</ul>