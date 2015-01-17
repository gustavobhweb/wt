<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Usuario extends Eloquent implements UserInterface, RemindableInterface
{
    
    use UserTrait, RemindableTrait;

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $fillable = [
        'cep',
        'cidade',
        'cpf',
        'curso',
        'email',
        'matricula',
        'nivel_id',
        'nome',
        'numero',
        'password',
        'protocolo',
        'turno',
        'uf',
        'username',
    ];

    public function nivel()
    {
        return $this->belongsTo('Nivel', 'nivel_id');
    }

    public function creditos()
    {
        return $this->hasMany('Credito', 'usuario_id');
    }

    public function ultimaSolicitacao()
    {
        return $this->hasOne('Solicitacao', 'usuario_id')->orderBy('created_at', 'desc');
    }

    public function instituicaoPadrao()
    {
        return $this->hasOne('UsuarioInstituicao', 'usuario_id');
    }



}
