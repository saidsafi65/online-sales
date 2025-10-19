<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model {
    protected $fillable = ['name', 'location', 'phone', 'description'];

    public function users() {
        return $this->hasMany(User::class);
    }

    public function sales() {
        return $this->hasMany(Sale::class);
    }

    public function repairs() {
        return $this->hasMany(Repair::class);
    }
}