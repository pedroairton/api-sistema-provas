<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $fillable = ['nome', 'email', 'senha_hash'];

    public $timestamps = false;

    protected $hidden = ['senha_hash'];

    public function respostas()
    {
        return $this->hasMany(UsuarioResposta::class);
    }
}
