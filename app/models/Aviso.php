<?php

class Aviso extends Eloquent
{

    protected $fillable = [
        'assunto',
        'remetente',
        'mensagem',
        'lido',
        'usuario_id'
    ];

    /**
     * Envia uma mensagem para o usuário baseando-se no id da solicitação
     * 
     * @param int $solicitacao_id
     *            O id da solicitação
     * @param string $mensagem
     *            O corpo da mensagem
     * @param string $assunto(optional)
     *            O assunto da mensagem
     * @return Aviso A instância do model Aviso
     *        
     */
    public static function solicitacao($solicitacao_id, $mensagem, $assunto = 'solicitação')
    {
        $solicitacao = Solicitacao::find($solicitacao_id);
        
        if ($solicitacao instanceof Solicitacao) {
            
            return Aviso::create([
                'usuario_id' => $solicitacao->usuario_id,
                'assunto' => $assunto,
                'remetente' => 'Newton Paiva',
                'mensagem' => $mensagem,
                'lido' => '0'
            ]);
        } else {
            
            throw new Exception(sprintf('A solicitãção %04s  não foi encontrada', $solicitacao_id));
        }
    }



    public static function read($id)
    {
        $auth = Auth::user();
        $aviso = static::whereUsuarioId($auth->id)
                        ->whereId($id)
                        ->firstOrFail();  

        if (! $aviso->lido) {
            $aviso->lido = 1;
            $aviso->save();
        }

        return $aviso;
    }
}