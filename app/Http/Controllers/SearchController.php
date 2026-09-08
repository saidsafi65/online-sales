<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\CustomerOrder;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Repair;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    private const PER_CATEGORY_LIMIT = 5;

    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $like = '%' . $q . '%';
        $user = auth()->user();
        $results = [];

        if ($user->canViewSection('catalog')) {
            $items = CatalogItem::where('product', 'like', $like)
                ->orWhere('type', 'like', $like)
                ->limit(self::PER_CATEGORY_LIMIT)->get();
            if ($items->isNotEmpty()) {
                $results['catalog'] = [
                    'label' => 'الكتالوج',
                    'icon' => 'fa-boxes-stacked',
                    'items' => $items->map(fn ($i) => [
                        'title' => $i->product . ($i->type ? ' - ' . $i->type : ''),
                        'subtitle' => 'الكمية: ' . $i->quantity . ' | ' . number_format((float) $i->sale_price, 2) . ' شيكل',
                        'url' => route('catalog.edit', $i->id),
                    ]),
                ];
            }
        }

        if ($user->canViewSection('invoices')) {
            $items = Invoice::where('customer_name', 'like', $like)
                ->orWhere('invoice_number', 'like', $like)
                ->limit(self::PER_CATEGORY_LIMIT)->get();
            if ($items->isNotEmpty()) {
                $results['invoices'] = [
                    'label' => 'الفواتير',
                    'icon' => 'fa-file-invoice',
                    'items' => $items->map(fn ($i) => [
                        'title' => 'فاتورة #' . $i->invoice_number,
                        'subtitle' => $i->customer_name . ' | ' . number_format((float) $i->afterDiscount_amount, 2) . ' شيكل',
                        'url' => route('invoices.show', $i),
                    ]),
                ];
            }
        }

        if ($user->canViewSection('repairs')) {
            $items = Repair::where('customer_name', 'like', $like)
                ->orWhere('device_name', 'like', $like)
                ->orWhere('model', 'like', $like)
                ->orWhere('phone', 'like', $like)
                ->limit(self::PER_CATEGORY_LIMIT)->get();
            if ($items->isNotEmpty()) {
                $results['repairs'] = [
                    'label' => 'الصيانة',
                    'icon' => 'fa-screwdriver-wrench',
                    'items' => $items->map(fn ($r) => [
                        'title' => $r->customer_name . ' - ' . $r->device_name,
                        'subtitle' => ($r->model ? $r->model . ' | ' : '') . $r->phone,
                        'url' => route('repairs.edit', $r),
                    ]),
                ];
            }
        }

        if ($user->canViewSection('customer_orders')) {
            $items = CustomerOrder::where('customer_name', 'like', $like)
                ->orWhere('phone_number', 'like', $like)
                ->limit(self::PER_CATEGORY_LIMIT)->get();
            if ($items->isNotEmpty()) {
                $results['customer_orders'] = [
                    'label' => 'طلبات العملاء',
                    'icon' => 'fa-clipboard-list',
                    'items' => $items->map(fn ($o) => [
                        'title' => $o->customer_name . ' - ' . $o->device_type,
                        'subtitle' => $o->phone_number . ' | ' . $o->status_label,
                        'url' => route('customer-orders.show', $o),
                    ]),
                ];
            }
        }

        if ($user->canViewSection('online_orders')) {
            $items = Order::where('customer_name', 'like', $like)
                ->orWhere('customer_phone', 'like', $like)
                ->limit(self::PER_CATEGORY_LIMIT)->get();
            if ($items->isNotEmpty()) {
                $results['online_orders'] = [
                    'label' => 'الطلبات الإلكترونية',
                    'icon' => 'fa-cart-shopping',
                    'items' => $items->map(fn ($o) => [
                        'title' => 'طلب #' . $o->id . ' - ' . $o->customer_name,
                        'subtitle' => $o->customer_phone . ' | ' . number_format((float) $o->total, 2) . ' شيكل',
                        'url' => route('online-orders.show', $o),
                    ]),
                ];
            }
        }

        if ($user->canViewSection('sales')) {
            $items = Sale::where('product', 'like', $like)
                ->orWhere('type', 'like', $like)
                ->limit(self::PER_CATEGORY_LIMIT)->get();
            if ($items->isNotEmpty()) {
                $results['sales'] = [
                    'label' => 'المبيعات',
                    'icon' => 'fa-cash-register',
                    'items' => $items->map(fn ($s) => [
                        'title' => $s->product . ($s->type ? ' - ' . $s->type : ''),
                        'subtitle' => 'الكمية: ' . $s->quantity . ' | ' . $s->sale_date?->format('Y-m-d'),
                        'url' => route('sales.show', $s),
                    ]),
                ];
            }
        }

        return response()->json(['results' => $results]);
    }
}
