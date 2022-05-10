<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Transition_Table extends Model
{
    use HasFactory;

    public $table = "transition_tables";

    public function fa(){
        return $this->belongsTo(FA::class);
    }

    public function transitions(){
        return $this->hasMany(Transition::class, 'transition_table_id');
    }

    public static function storeTrasition_Table($fa_id){
        $transition_table = new Transition_Table();
        $transition_table->fa_id = $fa_id;
        $transition_table->save();
        Session::put('transition_table_id', $transition_table->id);

    }
}
