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