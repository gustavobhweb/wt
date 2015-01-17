<ul>
    <li>
        <a href='{{ URL::to("cliente/inicial") }}' {{ (Request::is('cliente/inicial')) ? 'class="on"' : '' }} >
            <span class="glyphicon glyphicon-home"></span>
            Página inicial
        </a>
    </li>
    <li>
        <a href='{{ URL::to("cliente/enviar-carga") }}' {{ (Request::is('cliente/enviar-carga')) ? 'class="on"' : '' }} >
            <span class="glyphicon glyphicon-arrow-up"></span>
            Enviar carga
        </a>
    </li>
    <li>
        <a href='{{ URL::to("cliente/baixar-carga") }}' {{ (Request::is('cliente/baixar-carga')) ? 'class="on"' : '' }} >
            <span class="glyphicon glyphicon-download-alt"></span>
            Baixar Carga
        </a>
    </li>
    <li>
        <a href='{{ URL::to("cliente/pesquisar-alunos") }}' {{ (Request::is( 'cliente/pesquisar-alunos')) ? 'class="on"' : '' }}>
            <span class="glyphicon glyphicon-search"></span> Pesquisar Alunos
        </a>
    </li>
</ul>