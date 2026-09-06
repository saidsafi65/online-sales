<?php

namespace App\Services;

use App\Models\CatalogItem;
use App\Models\Product;

/**
 * A Product linked to a CatalogItem (`catalog_item_id`) has its stock owned
 * by that catalog item — CatalogItem::syncProductStock() overwrites
 * Product.quantity to match the catalog on every catalog save/increment/
 * decrement. Any code that decremented Product.quantity directly for a
 * catalog-linked product had its change silently erased the next time the
 * catalog item was touched (e.g. a POS restock). This service makes sure
 * every stock change for a catalog-linked product goes through the catalog
 * item itself, so the two never drift apart.
 */
class ProductStockService
{
    public static function decrement(Product $product, int $quantity): void
    {
        $catalogItem = static::lockedCatalogItemFor($product);

        if ($catalogItem) {
            $catalogItem->decrement('quantity', $quantity); // cascades to Product via CatalogItem::syncProductStock()
            return;
        }

        $product->decrement('quantity', $quantity);

        $fresh = $product->fresh();
        if ($fresh->quantity <= 0 && ! $fresh->is_out_of_stock) {
            $fresh->update(['is_out_of_stock' => true]);
        }
    }

    public static function increment(Product $product, int $quantity): void
    {
        $catalogItem = static::lockedCatalogItemFor($product);

        if ($catalogItem) {
            $catalogItem->increment('quantity', $quantity);
            return;
        }

        $product->increment('quantity', $quantity);

        $fresh = $product->fresh();
        if ($fresh->is_out_of_stock && $fresh->quantity > 0) {
            $fresh->update(['is_out_of_stock' => false]);
        }
    }

    private static function lockedCatalogItemFor(Product $product): ?CatalogItem
    {
        if (! $product->catalog_item_id) {
            return null;
        }

        return CatalogItem::withoutGlobalScopes()
            ->where('id', $product->catalog_item_id)
            ->lockForUpdate()
            ->first();
    }
}
