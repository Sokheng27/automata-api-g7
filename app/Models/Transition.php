<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transition extends Model
{
    use HasFactory;
    public $table = "transitions";

    protected $fillable = ['from_state_id', 'to_state_id', 'input', 'transition_table_id'];

    public static function storeTransition($transition, $transition_table_id){

        foreach ($transition as $value){
            self::create([
                'from_state_id'   => $value['from'] ?? null,
                'input' => $value['input'] ?? null,
                'to_state_id' => json_encode($value['to']) ?? null,
                'transition_table_id' => $transition_table_id ?? null
            ]);
        }

    }

    public function checkTransitionLength($transition){
        return $transition->name;

    }
}
