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
            $check_symbol = in_array('ε', json_decode($fa->symbol));
            if ($check_symbol == true) {
                return $this->fail(new FACollection(FA::where('id', $fa->id)->get()));
            }
            foreach ($fa->transition_tables->transitions as $key => $transition) {

                if (count(json_decode($transition->to_state_id)) == 0 || count(json_decode($transition->to_state_id)) > 1) {
                    $isDFA = false;
                }
            }
            if ($isDFA == true) {
                return $this->ok(new FACollection(FA::where('id', $fa->id)->get()));
            } elseif ($isDFA == false) {
                return $this->fail(new FACollection(FA::where('id', $fa->id)->get()));
            }

        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }

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
                $check_symbol = in_array($symbol, json_decode($fa->symbol));

                if ($check_symbol == false) {
                    $symbol_not_founds[] = $symbol;
                    return $this->fail($symbol, "This symbol not found");
                }

                if ($key == 0) {
                    $from = json_encode($currentState);
                } else {
                    foreach (json_decode($currentState->from_state_id) as $from_state_id){
                        $from = $from_state_id;
                    }

                }

                $currentState = $fa->transition_tables->transitions->where('input', $symbol)->where('from_state_id', $from)->first();

                if ($currentState == null) {

                    return  $this->fail($from,"State not found ");
                }
dd($currentState->to_state_id);

            }
                return $currentState;
            }catch (\Exception $e) {
                return $this->fail($e->getMessage());
        }
    }
}
