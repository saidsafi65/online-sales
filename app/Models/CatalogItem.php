<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\HasBranchScope;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;

class CatalogItem extends Model
{
    use HasFactory, HasBranchScope;
    protected $table = 'catalog_items';
    protected $fillable = [
        'product',
        'type',
        'barcode',
        'quantity',
        'wholesale_price',
        'sale_price',
    ];

    protected static function booted()
    {
        // يغطي أي حفظ عادي (save/update/create) على عنصر الكتالوج
        static::saved(function (CatalogItem $item) {
            static::syncProductStock($item);
        });

        // إشعار "عنصر جديد بالكتالوج"
        static::created(function (CatalogItem $item) {
            NotificationService::notifyCrud(
                'catalog_created',
                'عنصر جديد بالكتالوج',
                $item->product . ($item->type ? ' — ' . $item->type : ''),
                '/catalog/' . $item->id . '/edit',
                $item->branch_id,
                static::class,
                $item->id
            );
        });

        // إشعار "تعديل على الكتالوج"
        static::updated(function (CatalogItem $item) {
            NotificationService::notifyCrud(
                'catalog_updated',
                'تعديل على الكتالوج',
                $item->product . ($item->type ? ' — ' . $item->type : ''),
                '/catalog/' . $item->id . '/edit',
                $item->branch_id,
                static::class,
                $item->id
            );
        });

        // إشعار "حذف من الكتالوج" (قبل الحذف الفعلي، عشان البيانات لسا موجودة)
        static::deleting(function (CatalogItem $item) {
            NotificationService::notifyCrud(
                'catalog_deleted',
                'حذف من الكتالوج',
                $item->product . ($item->type ? ' — ' . $item->type : ''),
                null,
                $item->branch_id,
                static::class,
                $item->id
            );
        });
    }

    /**
     * increment()/decrement() في Eloquent ما بيطلقوا أحداث الموديل (saved/updated)
     * لأنهم بيحدّثوا قاعدة البيانات مباشرة، فلازم نتعامل معهم يدويًا هون.
     */
    public function decrement($column, $amount = 1, $extra = [])
    {
        $result = parent::decrement($column, $amount, $extra);
        if ($column === 'quantity') {
            static::syncProductStock($this);
        }
        return $result;
    }

    public function increment($column, $amount = 1, $extra = [])
    {
        $result = parent::increment($column, $amount, $extra);
        if ($column === 'quantity') {
            static::syncProductStock($this);
        }
        return $result;
    }

    /**
     * يحدّث حالة "نفذت الكمية" تلقائيًا للمنتجات واللابتوبات المرتبطة بهذا العنصر
     * من الكتالوج، وبيولّد إشعارات المخزون (منتجات/لابتوبات/الكتالوج نفسه).
     */
    protected static function syncProductStock(CatalogItem $item)
    {
        if (! $item->id) {
            return;
        }
    
        $freshItem = static::withoutGlobalScopes()->find($item->id);
        if (! $freshItem) {
            return;
        }
    
        $quantity = (int) $freshItem->quantity;
        $outOfStock = $quantity <= 0;
    
        // الطريقة الموصى بها: منتجات مربوطة مباشرة بهذا العنصر من صفحة إنشاء/تعديل المنتج
        Product::where('catalog_item_id', $item->id)
            ->update(['quantity' => $quantity, 'is_out_of_stock' => $outOfStock]);
    
        // احتياط: منتجات قديمة غير مربوطة بعد، بس اسمها مطابق تمامًا لاسم عنصر الكتالوج
        if ($item->product) {
            Product::whereNull('catalog_item_id')
                ->where('name', $item->product)
                ->update(['quantity' => $quantity, 'is_out_of_stock' => $outOfStock]);
        }
    
        // نفس المنطق للابتوبات المربوطة
        SaleLaptop::where('catalog_item_id', $item->id)
            ->update(['quantity' => $quantity, 'is_out_of_stock' => $outOfStock]);
    
        // إشعارات المخزون: للمنتجات واللابتوبات المرتبطة، ولعنصر الكتالوج نفسه
        Product::where('catalog_item_id', $item->id)->get()
            ->each(fn ($p) => NotificationService::syncStock($p));
    
        SaleLaptop::where('catalog_item_id', $item->id)->get()
            ->each(fn ($l) => NotificationService::syncStock($l));
    
        NotificationService::syncCatalogStock($freshItem);
    }
}