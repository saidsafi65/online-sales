<?php
namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchManagementController extends Controller {
    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index() {
        $branches = Branch::withCount('users')->paginate(15);
        return view('branches.index', compact('branches'));
    }

    public function create() {
        return view('branches.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:branches',
            'location' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', 'تم إضافة الفرع بنجاح');
    }

    public function edit(Branch $branch) {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch) {
        $validated = $request->validate([
            'name' => "required|string|max:255|unique:branches,name,{$branch->id}",
            'location' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'تم تحديث الفرع بنجاح');
    }

    public function destroy(Branch $branch) {
        if ($branch->users()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف فرع يحتوي على مستخدمين');
        }

        $branch->delete();

        return redirect()->back()->with('success', 'تم حذف الفرع بنجاح');
    }
}