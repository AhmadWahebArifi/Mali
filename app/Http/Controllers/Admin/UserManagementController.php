<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
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

    /**
     * Get users data as JSON for dynamic updates
     */
    public function getUsersData()
    {
        // Only admin can access this
        if (Auth::user()->email !== 'admin@mali.com') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $pendingUsers = User::where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->diffForHumans(),
                ];
            });
            
        $approvedUsers = User::where('is_approved', true)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'approved_at' => $user->approved_at?->diffForHumans(),
                    'is_admin' => $user->email === 'admin@mali.com',
                ];
            });
        
        return response()->json([
            'pending' => $pendingUsers,
            'approved' => $approvedUsers,
            'pending_count' => $pendingUsers->count(),
            'approved_count' => $approvedUsers->count(),
        ]);
    }
    
    public function approve(User $user)
    {
        // Only admin can approve users
        if (Auth::user()->email !== 'admin@mali.com') {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }
        
        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);
        
        // Create notification for approved user
        Notification::createForUser(
            $user->id,
            'Account Approved',
            'Your account has been approved by the administrator. You can now log in.',
            'success'
        );
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User approved successfully!',
                'user_id' => $user->id
            ]);
        }
        
        return back()->with('success', 'User approved successfully!');
    }
    
    public function reject(User $user)
    {
        // Only admin can reject users
        if (Auth::user()->email !== 'admin@mali.com') {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }
        
        $user->delete();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User rejected and removed successfully!',
                'user_id' => $user->id
            ]);
        }
        
        return back()->with('success', 'User rejected and removed successfully!');
    }
}
