<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DcConversation extends Model
{
    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class,'conversation_id');
    }
}
