<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'points', 'declared_at'];

    /**
     * Get the user associated with the winner.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
