<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
