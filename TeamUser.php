<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamUser extends Model
{
    protected $fillable = ['team_id', 'name', 'vote'];

    // Bu kullanıcının bağlı olduğu takım ilişkisi
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}