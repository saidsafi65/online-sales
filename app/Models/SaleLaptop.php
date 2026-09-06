<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\NotificationService;

class SaleLaptop extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'brand',
        'model',
        'processor',
        'ram',
        'storage',
        'gpu',
        'battery_life',
        'price',
        'discount',
        'description',
        'is_out_of_stock',
        'quantity',
        'catalog_item_id',
        'branch_id',
    ];
    protected $casts = [
        'price'            => 'decimal:2',
        'discount'         => 'decimal:2',
        'is_out_of_stock'  => 'boolean',
        'quantity'         => 'integer',
    ];

    protected static function booted()
    {
        static::created(function (SaleLaptop $laptop) {
            NotificationService::notifyCrud(
                'laptop_created',
                'لابتوب جديد',
                $laptop->name,
                '/laptops/' . $laptop->id . '/edit',
                $laptop->branch_id,
                static::class,
                $laptop->id
            );
        });

        static::updated(function (SaleLaptop $laptop) {
            $changes = array_keys($laptop->getChanges());
            $onlySystemFields = collect($changes)->diff(['is_out_of_stock', 'updated_at'])->isEmpty();

            if ($onlySystemFields && !empty($changes)) {
                return;
            }

            NotificationService::notifyCrud(
                'laptop_updated',
                'تعديل على لابتوب',
                $laptop->name,
                '/laptops/' . $laptop->id . '/edit',
                $laptop->branch_id,
                static::class,
                $laptop->id
            );
        });

        static::deleting(function (SaleLaptop $laptop) {
            NotificationService::notifyCrud(
                'laptop_deleted',
                'حذف لابتوب',
                $laptop->name,
                null,
                $laptop->branch_id,
                static::class,
                $laptop->id
            );
        });
    }

    public function images()
    {
        return $this->hasMany(SaleLaptopImage::class);
    }
    public function mainImage()
    {
        return $this->hasOne(SaleLaptopImage::class)->oldestOfMany();
    }
    public function catalogItem()
    {
        return $this->belongsTo(CatalogItem::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function getFinalPriceAttribute()
    {
        return $this->discount > 0
            ? $this->price * (1 - $this->discount / 100)
            : (float) $this->price;
    }
}