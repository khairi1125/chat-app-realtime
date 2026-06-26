<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'status',
        'last_seen_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_seen_at'      => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Relasi ke conversations (sebagai anggota)
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
                    ->withPivot('role', 'joined_at', 'last_read_at')
                    ->withTimestamps();
    }

    // Relasi ke messages
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Cek apakah user sedang online
    public function isOnline(): bool
    {
        return $this->status === 'online';
    }
}