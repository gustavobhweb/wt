<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DefaultDBData extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:db_insert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Esse comando irá inserir os dados principais dessa aplicação';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        if ($this->confirm('"setores"? [y|n]')) {
            
            DB::table('setores')->delete();
            DB::table('setores')->insert([
                [
                    'id' => 1,
                    'nome' => 'Produção'
                ],
                [
                    'id' => 2,
                    'nome' => 'Logística'
                ],
                [
                    'id' => 3,
                    'nome' => 'Financeiro'
                ]
            ]);
        }
        
        if ($this->confirm('"tipos_codigo_cartao"? [y|n]')) {
            
            DB::table('tipos_codigo_cartao')->delete();
            DB::table('tipos_codigo_cartao')->insert([
                [
                    'id' => '1',
                    'nome' => 'Código W'
                ]
            ]);
        }
        
        if ($this->confirm('"tipos_cartao"? [y|n]')) {
            
            DB::table('tipos_cartao')->delete();
            DB::table('tipos_cartao')->insert([
                [
                    'id' => '1',
                    'nome' => 'Cartão comum PVC 0,76'
                ],
                [
                    'id' => '2',
                    'nome' => 'Cartão comum 0,30'
                ],
                [
                    'id' => '3',
                    'nome' => 'Cartão mifare 13,56 (quadrado)'
                ],
                [
                    'id' => '4',
                    'nome' => 'Cartão acura 125 (redondo)'
                ],
                [
                    'id' => '5',
                    'nome' => 'Cartão com tarja magnética'
                ],
                [
                    'id' => '6',
                    'nome' => 'Cartão com adesivo'
                ],
                [
                    'id' => '7',
                    'nome' => 'Cartão UHF'
                ]
            ]);
        }
        
        if ($this->confirm('"tipos_entrega"? [y|n]')) {
            
            DB::table('tipos_entrega')->delete();
            DB::table('tipos_entrega')->insert([
                [
                    'id' => '1',
                    'nome' => 'Balcão'
                ],
                [
                    'id' => '2',
                    'nome' => 'Motoboy'
                ],
                [
                    'id' => '3',
                    'nome' => 'PAC'
                ],
                [
                    'id' => '4',
                    'nome' => 'SEDEX'
                ],
                [
                    'id' => '5',
                    'nome' => 'Registrada'
                ],
                [
                    'id' => '6',
                    'nome' => 'Transporte'
                ]
            ]);
        }
        
        if ($this->confirm('"modelos_cartao" [y|n]')) {
            
            DB::table('modelos_cartao')->delete();
            DB::table('modelos_cartao')->insert([
                [
                    'id' => 1,
                    'nome' => 'Aluno',
                    'tem_furo' => 1,
                    'tem_cv' => 1,
                    'impressao_termica' => 0,
                    'tipo_codigo_cartao_id' => 1,
                    'tipo_cartao_id' => 1,
                    'tipo_entrega_id' => 1
                ]
            ]);
        }
		
		if ($this->confirm('"tipos_cartao"? [y|n]')) {
            
            DB::table('tipos_cartao')->insert([
			
		}
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array(
                'example',
                null,
                InputOption::VALUE_OPTIONAL,
                'An example option.',
                null
            )
        );
    }
}
