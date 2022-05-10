<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TransitionCollection extends ResourceCollection
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
                    'id' => $data->id,
                    'from_state_id' => $data->from_state_id,
                    'input' => $data->input,
                    'to_state_id' => json_decode($data->to_state_id),
                    'transition_table_id' => json_decode($data->transition_table_id),

                ];
            })
        ];
    }
}
