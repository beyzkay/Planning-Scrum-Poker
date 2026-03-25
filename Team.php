<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'slug', 'is_revealed'];

    public function users()
    {
        return $this->hasMany(TeamUser::class);
    }
}