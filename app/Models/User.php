<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'branch_id', 'role', 'status',
        'can_view_sales','can_view_repairs','can_view_purchases','can_view_catalog','can_view_deposits',
        'can_view_reports','can_view_obligations','can_view_invoices','can_view_compatibility','can_view_customer_orders',
        'can_view_daily_handovers','can_view_returned_goods','can_view_store','can_view_debts','can_view_backup',
        'can_view_maintenance_parts','can_view_products'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'can_view_sales' => 'boolean',
            'can_view_repairs' => 'boolean',
            'can_view_purchases' => 'boolean',
            'can_view_catalog' => 'boolean',
            'can_view_deposits' => 'boolean',
            'can_view_reports' => 'boolean',
            'can_view_obligations' => 'boolean',
            'can_view_invoices' => 'boolean',
            'can_view_compatibility' => 'boolean',
            'can_view_customer_orders' => 'boolean',
            'can_view_daily_handovers' => 'boolean',
            'can_view_returned_goods' => 'boolean',
            'can_view_store' => 'boolean',
            'can_view_debts' => 'boolean',
            'can_view_backup' => 'boolean',
            'can_view_maintenance_parts' => 'boolean',
            'can_view_products' => 'boolean',
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

    public function canViewSection(string $key): bool {
        if ($this->isAdmin()) return true;
        $map = [
            'sales' => 'can_view_sales',
            'repairs' => 'can_view_repairs',
            'purchases' => 'can_view_purchases',
            'catalog' => 'can_view_catalog',
            'deposits' => 'can_view_deposits',
            'reports' => 'can_view_reports',
            'obligations' => 'can_view_obligations',
            'invoices' => 'can_view_invoices',
            'compatibility' => 'can_view_compatibility',
            'customer_orders' => 'can_view_customer_orders',
            'daily_handovers' => 'can_view_daily_handovers',
            'returned_goods' => 'can_view_returned_goods',
            'store' => 'can_view_store',
            'debts' => 'can_view_debts',
            'backup' => 'can_view_backup',
            'maintenance_parts' => 'can_view_maintenance_parts',
            'products' => 'can_view_products',
        ];
        $column = $map[$key] ?? null;
        return $column ? (bool) $this->{$column} : false;
    }
}
