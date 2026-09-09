<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\BankOfPalestineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BankOfPalestineController extends Controller
{
    public function __construct(protected BankOfPalestineService $bankOfPalestine) {}

    /**
     * الصفحة يلي بيرجع لها الزبون بعد الدفع (سواء نجح أو فشل)
     */
    public function return(Order $order)
    {
        return view('checkout.result', compact('order'));
    }

    /**
     * بوابة دفع وهمية مؤقتة — تُستخدم فقط لما بيانات بنك فلسطين الحقيقية مش
     * موجودة بعد، حتى نقدر نجرب مسار الدفع كامل. بتتوقف تلقائياً بمجرد ما
     * تنضاف بيانات البنك الحقيقية (credentialsConfigured() بترجع true).
     */
    public function mockGateway(Order $order)
    {
        abort_if($this->bankOfPalestine->credentialsConfigured(), 404);
        abort_unless($order->customer_id === Auth::guard('customer')->id(), 403);

        if ($order->status !== 'pending') {
            return redirect()->route('bankofpalestine.return', $order);
        }

        return view('checkout.mock-gateway', [
            'order' => $order,
            'gatewayLabel' => 'بنك فلسطين',
            'gatewayIcon' => 'fa-landmark',
            'resolveRoute' => 'bankofpalestine.mock.resolve',
        ]);
    }

    /**
     * محاكاة رد بنك فلسطين (نجاح/فشل) — بتستدعي نفس منطق handleCallback()
     * الحقيقي يلي بيستخدمه الـ Webhook الفعلي، فقط بدون التحقق من التوقيع
     * (هون الثقة مصدرها auth:customer + التحقق من ملكية الطلب، مش توقيع خارجي).
     */
    public function mockResolve(Request $request, Order $order)
    {
        abort_if($this->bankOfPalestine->credentialsConfigured(), 404);
        abort_unless($order->customer_id === Auth::guard('customer')->id(), 403);

        if ($order->status !== 'pending') {
            return redirect()->route('bankofpalestine.return', $order);
        }

        $validated = $request->validate(['result' => 'required|in:success,failed']);

        $this->bankOfPalestine->handleCallback([
            'order_id'       => $order->id,
            'status'         => $validated['result'],
            'transaction_id' => 'MOCK-' . Str::random(10),
            'amount'         => $order->total,
        ]);

        return redirect()->route('bankofpalestine.return', $order);
    }

    /**
     * الـ Webhook يلي بنك فلسطين بيبعثله تحديث الحالة (Server-to-server)
     */
    public function callback(Request $request)
    {
        $signature = $request->header('X-BOP-Signature', '');

        if (!$this->bankOfPalestine->verifyCallbackSignature($request->all(), $signature)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $this->bankOfPalestine->handleCallback($request->all());

        return response()->json(['status' => 'ok']);
    }
}
