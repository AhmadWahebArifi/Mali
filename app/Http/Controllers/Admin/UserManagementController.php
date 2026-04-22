<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        // Only admin can access this
        if (Auth::user()->email !== 'admin@mali.com') {
            abort(403);
        }
        
        $pendingUsers = User::where('is_approved', false)->get();
        $approvedUsers = User::where('is_approved', true)->get();
        
        return view('admin.users.index', compact('pendingUsers', 'approvedUsers'));
    }
    
    public function approve(User $user)
    {
        // Only admin can approve users
        if (Auth::user()->email !== 'admin@mali.com') {
            abort(403);
        }
        
        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);
        
        return back()->with('success', 'User approved successfully!');
    }
    
    public function reject(User $user)
    {
        // Only admin can reject users
        if (Auth::user()->email !== 'admin@mali.com') {
            abort(403);
        }
        
        $user->delete();
        
        return back()->with('success', 'User rejected and removed successfully!');
    }
}
