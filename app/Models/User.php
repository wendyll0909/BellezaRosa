<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'username', 
        'phone',
        'email',
        'password',
        'role',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
    // Add these methods to the User model
public function sentMessages()
{
    return $this->hasMany(Message::class, 'sender_id');
}

public function receivedMessages()
{
    return $this->hasMany(Message::class, 'receiver_id');
}

public function notifications()
{
    return $this->hasMany(Notification::class);
}

// Get unread messages count
public function getUnreadMessagesCountAttribute()
{
    return $this->receivedMessages()->unread()->count();
}

// Get unread notifications count
public function getUnreadNotificationsCountAttribute()
{
    return $this->notifications()->unread()->count();
}

// Get total unread count
public function getTotalUnreadCountAttribute()
{
    return $this->unread_messages_count + $this->unread_notifications_count;
}

    // Your existing relationships and methods...
    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
}