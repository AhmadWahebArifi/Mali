<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::where('is_active', true)->orderBy('sort_order');
        
        // Handle search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('question', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('answer', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('category', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        $faqs = $query->get();
        
        return view('faq.index', compact('faqs'));
    }
    
    public function search(Request $request)
    {
        $searchTerm = $request->get('q', '');
        
        $faqs = Faq::where('is_active', true)
            ->where(function($q) use ($searchTerm) {
                $q->where('question', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('answer', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('category', 'LIKE', "%{$searchTerm}%");
            })
            ->orderBy('sort_order')
            ->get();
            
        return response()->json([
            'faqs' => $faqs,
            'count' => $faqs->count()
        ]);
    }
}
