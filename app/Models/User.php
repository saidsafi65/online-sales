<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable {
    use HasFactory, Notifiable, HasPushSubscriptions;

    protected $fillable = [
        'name', 'email', 'password', 'branch_id', 'role', 'status',
        'permissions', 'is_mobile_shop_only',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'is_mobile_shop_only' => 'boolean',
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

    /**
     * فحص صلاحية مفصّلة "قسم.إجراء" مثل "sales.edit". المدير يتجاوز كل شيء.
     * الاسم hasPermission وليس can() لأن Authorizable (اللي جايي من Authenticatable)
     * بيفرض توقيع can($abilities, $arguments = []) وما بيصير نغيره.
     */
    public function hasPermission(string $key): bool {
        if ($this->isAdmin()) return true;
        return in_array($key, $this->permissions ?? [], true);
    }

    public function canViewSection(string $key): bool {
        return $this->hasPermission("{$key}.view");
    }
}
