<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioResposta extends Model
{
    protected $fillable = ['usuario_id', 'questao_id', 'alternativa_selecionada_id'];

    public $timestamps = false;

    protected $dates = 'answered_at';

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    public function questao()
    {
        return $this->belongsTo(Questao::class);
    }
    public function alternativaSelecionada()
    {
        return $this->belongsTo(Alternativa::class, 'alternativa_selecionada_id');
    }
}
