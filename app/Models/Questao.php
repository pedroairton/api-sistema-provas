<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Questao extends Model
{
    protected $table = 'questoes';
    protected $fillable = ['admin_id', 'titulo', 'dificuldade', 'categoria'];

    public $timestamps = false;

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    public function alternativas()
    {
        return $this->hasMany(Alternativa::class);
    }
    public function respostas()
    {
        return $this->hasMany(UsuarioResposta::class);
    }
    public function scopeNaoRespondidasPor($query, $usuarioId){
        return $query->whereNotExists(function($query) use ($usuarioId){
            $query->select(DB::raw(1))
            ->from('respostas_usuarios')
            ->whereColumn('respostas_usuarios.questao_id', 'questoes.id')
            ->where('respostas_usuarios.usuario_id', $usuarioId);
        });
    }
}
