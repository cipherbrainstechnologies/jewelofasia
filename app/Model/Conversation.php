<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $casts = [
        'user_id'    => 'integer',
        'checked'    => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
