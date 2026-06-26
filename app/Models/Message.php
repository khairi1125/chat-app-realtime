<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'body',
        'type',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    // Relasi ke conversation
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    // Relasi ke user pengirim
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}