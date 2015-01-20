<?php

class AdminController extends BaseController
{
	public function getGerenciarUsuarios()
	{
		return View::make('admin.gerenciar-usuarios', $vars);
	}

	public function anyCadastrarUsuarios()
	{
		if (Request::isMethod('post')) {
			$data = Input::all();
			if (Usuario::whereUsername($data['username'])->count()) {
				$vars['message'] = 'Este usuário já existe no banco de dados.';
			} elseif ($data['password'] == null || $data['password'] == '') {
				$vars['message'] = 'Digite a senha do usuário.';
			} elseif ($data['nome'] == null || $data['nome'] == '') {
				$vars['message'] = 'Digite o nome do usuário.';
			} elseif ($data['nivel_id'] == null || $data['nivel_id'] == '') {
				$vars['message'] = 'Selecione o nível do usuário.';
			} else {
				$data['password'] = Hash::make($data['password']);
				Usuario::create($data);
				$vars['message'] = 'Usuário cadastrado com sucesso.';
			}
		}
		$vars['niveis'] = Nivel::all();
		return View::make('admin.cadastrar-usuarios', $vars);
	}

	public function anyCadastrarPermissoes()
	{
		$vars = [];
		if (Request::isMethod('post')) {
			$data = Input::all();
			if (Permissao::whereAction($data['action'])->count()) {
				$vars['message'] = 'Já existe uma permissão cadastrado com essa ação.';
			} else {
				$permissao = Permissao::create($data);
				$vars['suggest'] = true;
				$vars['permissao_id'] = $permissao->id;
				$vars['message'] = 'Permissão cadastrada com sucesso.';
			}
		}
		$vars['permissoes'] = Permissao::all();
		return View::make('admin.cadastrar-permissoes', $vars);
	}

	public function anyAcl()
	{
		$vars['niveis'] = Nivel::all();
		$vars['permissoes'] = Permissao::all();

		if (Request::isMethod('post')) {
			$data = Input::all();
			if ($data['nivel_id'] == null || $data['nivel_id'] == '') {
				$vars['message'] = 'Selecione um nível.';
			} elseif ($data['permissao_id'] == null || $data['permissao_id'] == '') {
				$vars['message'] = 'Selecione uma permissão.';
			} elseif (NiveisPermissao::whereNivelId($data['nivel_id'])->wherePermissaoId($data['permissao_id'])->count()) {
				$vars['message'] = 'Esta permissão já foi concedida a este nível';
			} else {
				NiveisPermissao::create($data);
				$vars['message'] = 'Permissão concedida com sucesso.';
			}
		}

		return View::make('admin.acl', $vars);
	}

	public function anyPermicoes($nivel_id)
	{
		if (Request::isMethod('post')) {
			if (Input::get('permissao_id_del')) {
				NiveisPermissao::whereNivelId($nivel_id)
								->wherePermissaoId(Input::get('permissao_id_del'))
								->delete();
			} elseif (Input::get('permissao_define_home_id')) {
				NiveisPermissao::whereNivelId($nivel_id)
								 ->update([
								 	'is_home' => 0
								 ]);

				NiveisPermissao::whereNivelId($nivel_id)
								->wherePermissaoId(Input::get('permissao_define_home_id'))
								->update([
								 	'is_home' => 1
								]);
			} else {
				$data = Input::all();
				$data['nivel_id'] = $nivel_id;
				if ($data['permissao_id'] == null || $data['permissao_id'] == '') {
					$vars['message'] = 'Selecione uma permissão.';
				} elseif (NiveisPermissao::whereNivelId($nivel_id)->wherePermissaoId($data['permissao_id'])->count()) {
					$vars['message'] = 'Esta permissão já foi concedida a este nível';
				} else {
					NiveisPermissao::create($data);
					$vars['message'] = 'Permissão concedida com sucesso.';	
				}
			}
		}
		$vars['nivel_id'] = $nivel_id;
		$nivel = $vars['nivel'] = Nivel::whereId($nivel_id)->first();
		$vars['permissoes'] = $nivel->permissoes;
		$vars['permissoesAll'] = Permissao::all();
		$niveisPermissaoHome = NiveisPermissao::whereNivelId($nivel_id)
												   ->whereIs_home(1)
												   ->first();
		$vars['paginaInicialId'] = ($niveisPermissaoHome) ? $niveisPermissaoHome->permissao_id : 0;
		return View::make('admin.permissoes', $vars);
	}

	public function anyCadastrarNiveis()
	{
		$vars = [];

		if (Request::isMethod('post')) {
			$nome = Input::get('titulo');
			if (Nivel::whereTitulo($nome)->count()) {
				$vars['message'] = "O nível " . $nome . " já existe!";
			} else {
				Nivel::create([
					'titulo' => $nome
				]);
				$vars['message'] = "O nível " . $nome . " foi cadastrado com sucesso!";
			}
		}

		return View::make('admin.cadastrar-niveis', $vars);
	}

	public function getControleDeUsuarios()
    {
    	$vars['niveis'] = Nivel::where('id', '!=', 1)->get();
    	foreach ($vars['niveis'] as $k => $v) {
    		$vars['niveis'][$k]->usuarios = Usuario::whereNivelId($vars['niveis'][$k]->id)
    												->where('id', '!=', Auth::user()->id)
    												->get();
    	}
    	return View::make('admin.controle-de-usuarios', $vars);
    }

    public function postAjaxTrocarNivel()
    {
    	$nivel_id = Input::get('nivel_id');
    	$usuario_id = Input::get('usuario_id');

    	Usuario::whereId($usuario_id)
    			->update([
    				'nivel_id' => $nivel_id
    			]);
    	return Response::json(true);
    }

    public function getAjaxSearchMethods()
    {
    	$search = Input::get('search');

		if (!strpos($search, '@')) {
			$dir = dir(app_path('controllers'));
			$controllers = [];

			if ($search != null && $search != '') {
				while ($file = $dir->read()) {
					if (stristr($file, $search)) {
						$controllers[] = str_replace('.php', '', $file) . '@';
					}
				}
			}

			return Response::json($controllers);
		} else {
			
			$searchArr = explode('@', $search);
			$controller = $searchArr[0];
			$method = $searchArr[1];

			$methods = get_class_methods($controller);

			$returnMethods = [];

			if ($method != null && $method != '') {
				foreach ($methods as $methodIndex) {
					if (stristr($methodIndex, $method)) {
						$returnMethods[] = $controller . '@' . $methodIndex;
						if ($search == ($controller . '@' . $methodIndex)) {
							return Response::json([]);
						}
					}
				}
			}
			return Response::json($returnMethods);
		}
    }

    public function getAjaxMakeUrl()
    {
    	$action = explode('@', Input::get('action'));

    	$controller = Str::slug(snake_case(str_replace('Controller', '', $action[0])));

    	$method = explode('_', snake_case($action[1]));
    	$typeRequest = $method[0];
    	unset($method[0]);
    	$method = str_replace('_', '-', implode('-', $method));


    	//preg_replace('/^(get|post|delete|put)/', '', $method);

    	$url = $controller . '/' . $method;

    	$reflect = new ReflectionMethod($action[0], $action[1]);

    	foreach( $reflect->getParameters() as $param) {
    		if ($param->isOptional()) {
	    		$url .= '/{' . $param->getName() . '?}';
	    	} else {
	    		$url .= '/{' . $param->getName() . '}';
	    	}
    	}

    	return Response::json([
    		'url' => $url,
    		'type' => $typeRequest
    	]);
    }

    public function getSearchPermission()
    {
    	$search = Input::get('search');
    	$permissoes = Permissao::where('name', 'LIKE', '%' . $search . '%')
    			  				->orWhere('action', 'LIKE', '%' . $search . '%')
    			  				->orWhere('url', 'LIKE', '%' . $search . '%')
    			  				->limit(10)
    			  				->get();
    	return Response::json($permissoes);
    }

    public function anyInitWorktab()
    {
    	return View::make('admin.wt');
    }

    public function anyGerenciarNiveis()
    {
    	$vars['niveis'] = Nivel::all();

    	if (Request::isMethod('post')){
    		$nome = Input::get('nome-nivel');
    		if (!Nivel::whereTitulo($nome)->count()) {
    			$vars['error'] = "Já existe um nível cadastrado com o nome {$nome}";
    		} else {
    			Nivel::create([
    				'titulo' => $nome
    			]);
    		}
    	}

    	return View::make('admin.gerenciar-niveis', $vars);
    }

}