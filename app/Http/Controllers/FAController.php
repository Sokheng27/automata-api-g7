<?php

namespace App\Http\Controllers;

use App\Models\FA;
use App\Models\State;
use App\Models\Transition;
use App\Models\Transition_Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FAController extends Controller
{
    public function DesignDFA(Request $request){
        DB::beginTransaction();

        try {

            FA::StoreFA($request);
            $fa_id = session('fa_id');

            State::storeState($request->states);
            Transition_Table::storeTrasition_Table($fa_id);
            $transition_table_id = session('transition_table_id');
            foreach ($request->transition_table as $key => $transition){
                Transition::storeTransition($transition, $transition_table_id);
            }
            return 200;
            DB::commit();
            // all good
        } catch (\Exception $e) {
            return  $e->getMessage();
            DB::rollback();
            // something went wrong
            return 404;
        }
    }

    public function CheackFA(Request $request){
        try{
            $isDFA = true;
            $fa = FA::where('id', $request->fa_id)->first();

            foreach ($fa->transition_tables->transitions as $key => $transition){

                if(count(json_decode($transition->to_state_id)) == 0 || count(json_decode($transition->to_state_id)) > 1){
                    $isDFA = false;
                }
            }
            if($isDFA == true){
                return [
                  'data' => "DFA"
                ];
            }elseif ($isDFA == false){
                return [
                    'data' => "DFA"
                ];
            }

        }catch (\Exception $e) {
            return  $e->getMessage();
            // something went wrong
            return 404;
        }

    }

    public function acceptString(Request $request){
        try{
            $accepted = true;
            $fa = FA::where('id', $request->fa_id)->first();

            $currentState = $fa->stage->where('fa_id' , $fa->id)->where('is_start', 1)->first();

            $symbols = str_split($request->text);

            $symbol_not_founds = array();
            $test = array();
            foreach ( $symbols as $key => $symbol){
                $check_symbol = in_array($symbol, json_decode($fa->symbol));
                if($check_symbol == false){
                    $symbol_not_founds[] = $symbol;
                }

                $currentState = $fa->transition_tables->transitions->where('input', $symbol)->where('from_state_id', $currentState->name) ;

                if($currentState == null){
                    return [
                        "data" => $currentState->name
                    ];
                }

                $currentState =  $fa->transition_tables->transitions;
//                return $currentState;
//                $currentState = $fa->transition_tables->transitions->where('input', $symbol);
            }

            return  $currentState;



            if($this->CheackFA($request) == false){
                return 12;
            };

            foreach ($fa->transition_tables->transitions as $key => $transition){
                if(count(json_decode($transition->to_state_id)) == 0 || count(json_decode($transition->to_state_id)) > 1){
                    $isDFA = false;
                }
            }

            return $isDFA ? 'This FA is DFA' : 'This FA is NFA';
        }catch (\Exception $e) {
            return  $e->getMessage();
            // something went wrong
            return 404;
        }

    }
}
