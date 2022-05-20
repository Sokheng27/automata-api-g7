<?php

namespace App\Models;

use http\Env\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class FA extends Model
{
    use HasFactory;
    public $table = "fas";

    public function transition_tables(){
        return $this->hasOne(Transition_Table::class, 'fa_id');
    }

    public function stage(){
        return $this->hasMany(State::class, 'fa_id');
    }

    public static function storeFA($request){

        $fa = new FA();
        if($request->symbol != null){
            $fa->symbol = json_encode($request->symbol);
        }
        $fa->save();
        Session::put('fa_id', $fa->id);

    }


    public static function savenewFA($request){

        $fa = new FA();
        if($request->symbol != null){
            $fa->symbol = json_encode($request->symbol);
        }
        $fa->save();
        dd($fa->symbol);
        Session::put('fa_id', $fa->id);

        Session::put('fa_symbol', $fa->symbol);
    }

    public function checksymbol($symbol, $fa_symbol){

        if($check_symbol == false){
            return 111;
        }
    }
}
