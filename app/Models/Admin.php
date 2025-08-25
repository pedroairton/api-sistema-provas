<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens;
    protected $fillable = ['nome', 'email', 'senha_hash'];
    public $timestamps = false;
    protected $hidden = ['senha_hash'];

    public function questoes(){
        return $this->hasMany(Questao::class);
    }
}
