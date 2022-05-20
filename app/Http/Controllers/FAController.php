<?php

namespace App\Http\Controllers;

use App\Http\Resources\FACollection;
use App\Http\Resources\FAIDCollection;
use App\Models\FA;
use App\Models\State;
use App\Models\Transition;
use App\Models\Transition_Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FAController extends Controller
{
    public function getAllDFA()
    {

        try {
            $fa = FA::all();
            return $this->ok(new FACollection($fa), 'hello bro Leap');
        } catch (\Exception $exception) {

            DB::rollback();
            return $this->fail($exception->getMessage());
        }

    }

    public function DesignDFA(Request $request)
    {
        DB::beginTransaction();

        try {

            FA::StoreFA($request);
            $fa_id = session('fa_id');

            State::storeState($request->states);
            Transition_Table::storeTrasition_Table($fa_id);
            $transition_table_id = session('transition_table_id');
            foreach ($request->transition_table as $key => $transition) {
                Transition::storeTransition($transition, $transition_table_id);
            }
            session()->forget('fa_id');
            session()->forget('transition_table_id');
            DB::commit();
            return $this->ok($fa_id, 'Design DFA is Successfully');
            // all good
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->fail($exception->getMessage());
        }
    }

    public function CheackFA(Request $request)
    {
        try {
            $isDFA = true;

            $fa = FA::where('id', $request->fa_id)->first();

            // Check if symbol include ε
//            $check_symbol = in_array("", json_decode($fa->symbol));
//
//            if ($check_symbol == true) {
//                $isDFA = false;
//                return $isDFA;
////                return $this->fail(new FACollection(FA::where('id', $fa->id)->get()));
//            }
            foreach ($fa->transition_tables->transitions as $key => $transition) {

                if (count(json_decode($transition->to_state_id)) == 0 || count(json_decode($transition->to_state_id)) > 1) {
                    $isDFA = false;
                }
            }
            return $isDFA;

        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }

    }

    public function returnCheackFA(Request $request){
        $isDFA = $this->CheackFA($request);
        if ($isDFA == true) {
            return $this->ok(new FACollection(FA::where('id', $request->fa_id)->get()), "Deterministic Finite Automaton (DFA)");
        } elseif ($isDFA == false) {
            return $this->ok(new FACollection(FA::where('id', $request->fa_id)->get()),"Nondeterministic Finite Automaton (NFA)");
        }
        return $isDFA;
    }

    public function acceptString(Request $request)
    {
        try {
            $accepted = true;
            $fa = FA::where('id', $request->fa_id)->first();
            $currentState = $fa->stage->where('fa_id', $fa->id)->where('is_start', 1)->first();
            $symbols = str_split($request->text);
            $symbol_not_founds = array();
            $test = array();
            foreach ($symbols as $key => $symbol) {
//                $check_symbol = in_array($symbol, json_decode($fa->symbol));
//                if ($check_symbol == false) {
//                    $symbol_not_founds[] = $symbol;
//                    return $this->fail($symbol, "This symbol not found");
//                }
                if ($key == 0) {
                    $from = $currentState->name;
                } else {
                    $from = $currentState->from_state_id;
                }
                $currentState = $fa->transition_tables->transitions->where('input', $symbol)->where('from_state_id', $from)->first();
                if ($currentState == null) {
                    return $this->fail($from, "Stage not found");
                }
                if(count(json_decode($currentState->to_state_id)) == 0 ){
                    $accepted = false;
                }
            }
                return $accepted;
            }catch (\Exception $e) {
                return $this->fail($e->getMessage());
        }
    }
    public function returnacceptString(Request $request){
        $result = $this->acceptString($request);
        if($result === true){
            return $this->ok(new FACollection(FA::where('id', $request->fa_id)->get()),'This string Accepted');
        }elseif ($result === false){
            return $this->fail(new FACollection(FA::where('id', $request->fa_id)->get()),'This string Rejected');
        }
        return $result;
    }

    public function NFATODFA(Request $request){

        $isDFA = $this->CheackFA($request);

        if ($isDFA == true) {
            return $this->ok(new FACollection(FA::where('id', $request->fa_id)->get()), "The is DFA Not NFA");
        }

        FA::StoreFA($request);
        $fa_id = session('fa_id');
        $fa_symbol = FA::where('id', $request->fa_id)->pluck('symbol');


        Transition_Table::storeTrasition_Table($fa_id);
        $transition_table_id = session('transition_table_id');

        $implementedStates =  [];
        $statesToRun =  [];
        $createdStates = [];

        $newState = State::NewState();


        $createdStates[] = $newState;
        $statesToRun[] = $newState;
        $implementedStates[] = $newState;


        while(count($statesToRun) > 0){
            $currentState = $statesToRun[0];

            if($currentState->is_dead == 0){

            }
            return $currentState->is_dead;
        }

    }
}
