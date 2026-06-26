<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'avatar',
        'created_by',
    ];

    // Relasi ke users (anggota conversation)
    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_user')
                    ->withPivot('role', 'joined_at', 'last_read_at')
                    ->withTimestamps();
    }

    // Relasi ke messages
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Relasi ke user yang membuat conversation
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Ambil pesan terakhir
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    // Cek apakah conversation ini private atau group
    public function isGroup(): bool
    {
        return $this->type === 'group';
    }
}