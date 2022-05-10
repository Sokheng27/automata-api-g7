<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{

    use HasFactory;
    public $table = "states";

    protected $fillable = ['name', 'is_start', 'is_final', 'is_dead', 'fa_id'];

    public function FA(){
        return $this->belongsTo(FA::class);
    }


    public static function storeState($request)
    {
        foreach ($request as $state)
        {
            self::create([
                'name'   => $state['name'] ?? null,
                'is_start' => $state['is_start'] ?? 0,
                'is_final' => $state['is_final'] ?? 0,
                'is_dead' => $state['is_dead'] ?? 0,
                'fa_id' => session('fa_id') ?? 0,
            ]);
        }
    }
}
