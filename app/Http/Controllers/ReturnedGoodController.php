<?php

namespace App\Http\Controllers;

use App\Models\ReturnedGood;
use Illuminate\Http\Request;

class ReturnedGoodController extends Controller
{
    public function index()
    {
        $returnedGoods = ReturnedGood::latest()->paginate(15);
        
        $pendingCount = ReturnedGood::where('status', 'pending')->count();
        $returnedCount = ReturnedGood::where('status', 'returned')->count();
        $resolvedCount = ReturnedGood::whereIn('status', ['replaced', 'refunded'])->count();

        return view('returned-goods.index', compact(
            'returnedGoods', 
            'pendingCount', 
            'returnedCount', 
            'resolvedCount'
        ));
    }

    public function create()
    {
        return view('returned-goods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'reason' => 'required|string',
            'issue_discovered_date' => 'required|date',
            'status' => 'nullable|in:pending,returned,replaced,refunded',
            'notes' => 'nullable|string',
        ]);

        ReturnedGood::create($validated);

        return redirect()->route('returned-goods.index')
            ->with('success', 'تم إضافة البضاعة المرجعة بنجاح');
    }

    public function edit(ReturnedGood $returnedGood)
    {
        return view('returned-goods.edit', compact('returnedGood'));
    }

    public function update(Request $request, ReturnedGood $returnedGood)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'reason' => 'required|string',
            'issue_discovered_date' => 'required|date',
            'status' => 'required|in:pending,returned,replaced,refunded',
            'notes' => 'nullable|string',
        ]);

        $returnedGood->update($validated);

        return redirect()->route('returned-goods.index')
            ->with('success', 'تم تحديث البضاعة المرجعة بنجاح');
    }

    public function destroy(ReturnedGood $returnedGood)
    {
        $returnedGood->delete();

        return redirect()->route('returned-goods.index')
            ->with('success', 'تم حذف السجل بنجاح');
    }

    public function show(ReturnedGood $returnedGood)
    {
        return view('returned-goods.show', compact('returnedGood'));
    }
}