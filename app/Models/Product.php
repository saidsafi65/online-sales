<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Services\NotificationService;

class Product extends Model
{
    protected $fillable = [
        'name', 'price', 'discount', 'category', 'description', 'image',
        'is_out_of_stock', 'catalog_item_id', 'quantity', 'branch_id',
    ];

    protected $casts = [
        'is_out_of_stock' => 'boolean',
        'quantity'        => 'integer',
    ];

    protected static function booted()
    {
        static::created(function (Product $product) {
            NotificationService::notifyCrud(
                'product_created',
                'منتج جديد',
                $product->name,
                '/products/' . $product->id . '/edit',
                $product->branch_id,
                static::class,
                $product->id
            );
        });

        static::updated(function (Product $product) {
            // نتجاهل التحديثات اللي منشأها النظام نفسه (تحديث is_out_of_stock تلقائي من الكتالوج)
            // إذا كان هو التغيير الوحيد، ما منعتبره تعديل يدوي يستاهل إشعار
            $changes = array_keys($product->getChanges());
            $onlySystemFields = collect($changes)->diff(['is_out_of_stock', 'updated_at'])->isEmpty();

            if ($onlySystemFields && !empty($changes)) {
                return;
            }

            NotificationService::notifyCrud(
                'product_updated',
                'تعديل على منتج',
                $product->name,
                '/products/' . $product->id . '/edit',
                $product->branch_id,
                static::class,
                $product->id
            );
        });

        static::deleting(function (Product $product) {
            NotificationService::notifyCrud(
                'product_deleted',
                'حذف منتج',
                $product->name,
                null,
                $product->branch_id,
                static::class,
                $product->id
            );
        });
    }

    /**
     * عنصر الكتالوج المرتبط بهذا المنتج (اختياري) — لما يتحدد،
     * حالة "نفذت الكمية" بتنحدّث تلقائيًا حسب كمية هذا العنصر بالكتالوج.
     */
    public function catalogItem()
    {
        return $this->belongsTo(CatalogItem::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating(): float
    {
        return round((float) $this->reviews()->avg('rating'), 1);
    }

    public function reviewsCount(): int
    {
        return $this->reviews()->count();
    }

    /**
     * احسب السعر بعد الخصم
     */
    public function getFinalPrice()
    {
        return $this->price - ($this->price * $this->discount / 100);
    }

    /**
     * احسب قيمة الخصم
     */
    public function getDiscountAmount()
    {
        return $this->price * $this->discount / 100;
    }

    public function getFinalPriceAttribute(): float
    {
        return round($this->getFinalPrice(), 2);
    }
}