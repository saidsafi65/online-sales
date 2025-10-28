<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'branch_id', 'role', 'status'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // العلاقات
    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    // الصلاحيات
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public function isManager(): bool {
        return $this->role === 'manager';
    }

    public function isEmployee(): bool {
        return $this->role === 'employee';
    }

    public function isActive(): bool {
        return $this->status === 'active';
    }
}
