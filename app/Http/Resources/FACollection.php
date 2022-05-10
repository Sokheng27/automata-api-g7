<?php

namespace App\Http\Resources;

use App\Models\State;
use App\Models\Transition;
use App\Models\Transition_Table;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FACollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'fa_id' => $data->id,
                    'symbol' => json_decode($data->symbol),
                    'stage' => $this->Stage($data->id),
                    'transition_table' => $this->transition_table($data->id)

                ];
            })
        ];
    }

    public function Stage($fa_id){
        $stage = State::select('id','name','is_start','is_dead','is_final')->where('fa_id', $fa_id )->get();
        return $stage;
    }

    public function transition_table($fa_id){
        $transittion_table = Transition_Table::where('fa_id', $fa_id)->first()->id;
        return [
            'id' => $transittion_table,
            'transition' => $this->transition($transittion_table),
        ];
    }

    public function transition($transittion_table_id){
        $transittion = Transition::where('transition_table_id', $transittion_table_id)->get();

        return new TransitionCollection($transittion);
    }
}
