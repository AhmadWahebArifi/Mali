<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Get categories with transaction counts for current month
        $incomeCategories = Category::where('type', 'income')
            ->withCount(['transactions' => function($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('date', $currentMonth)
                      ->whereYear('date', $currentYear);
            }])
            ->get();
            
        $expenseCategories = Category::where('type', 'expense')
            ->withCount(['transactions' => function($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('date', $currentMonth)
                      ->whereYear('date', $currentYear);
            }])
            ->get();
        
        $totalCategories = Category::count();
        
        // Calculate most used categories
        $mostUsedIncome = Category::where('type', 'income')
            ->withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->first();
            
        $mostUsedExpense = Category::where('type', 'expense')
            ->withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->first();
        
        return view('categories.index', compact(
            'incomeCategories', 
            'expenseCategories', 
            'totalCategories',
            'mostUsedIncome',
            'mostUsedExpense'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'type' => 'required|in:income,expense',
        ]);

        $category = Category::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                'category' => $category
            ]);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
            ]);
        }

        return redirect()->route('categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'type' => 'required|in:income,expense',
        ]);

        $category->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!',
                'category' => $category
            ]);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        // Check if category has transactions
        $transactionCount = $category->transactions()->count();
        if ($transactionCount > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete category with {$transactionCount} transactions. Please delete or reassign transactions first."
                ]);
            }
            return redirect()->route('categories.index')
                ->with('error', "Cannot delete category with {$transactionCount} transactions. Please delete or reassign transactions first.");
        }

        $category->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!'
            ]);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
