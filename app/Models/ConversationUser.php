<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationUser extends Model
{
    protected $table = 'conversation_user';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'role',
        'joined_at',
        'last_read_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at'    => 'datetime',
            'last_read_at' => 'datetime',
        ];
    }
}