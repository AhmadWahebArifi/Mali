<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use App\Services\LoggingService;
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
    
    public function approve(Request $request, $userId)
    {
        // Only admin can approve users
        if (Auth::user()->email !== 'admin@mali.com') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }
        
        $user = User::findOrFail($userId);
        
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
        
        // Log the approval action
        LoggingService::logUserApproval($user, Auth::user());
        
        // Check if this is an AJAX request
        $isAjax = $request->header('X-Requested-With') === 'XMLHttpRequest' || 
                  $request->header('Accept') === 'application/json' ||
                  $request->expectsJson();
        
        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => 'User approved successfully!',
                'user_id' => $user->id
            ]);
        }
        
        return back()->with('success', 'User approved successfully!');
    }
    
    public function reject(Request $request, $userId)
    {
        // Only admin can reject users
        if (Auth::user()->email !== 'admin@mali.com') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }
        
        $user = User::findOrFail($userId);
        
        // Log the rejection action before deletion
        LoggingService::logUserRejection($user, Auth::user());
        
        $user->delete();
        
        // Check if this is an AJAX request
        $isAjax = $request->header('X-Requested-With') === 'XMLHttpRequest' || 
                  $request->header('Accept') === 'application/json' ||
                  $request->expectsJson();
        
        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => 'User rejected and removed successfully!',
                'user_id' => $user->id
            ]);
        }
        
        return back()->with('success', 'User rejected and removed successfully!');
    }
    
    public function destroy(Request $request, $userId)
    {
        // Only admin can delete users
        if (Auth::user()->email !== 'admin@mali.com') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }
        
        $user = User::findOrFail($userId);
        
        // Prevent deletion of the main admin account
        if ($user->email === 'admin@mali.com') {
            if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the main admin account'
                ], 403);
            }
            return back()->with('error', 'Cannot delete the main admin account');
        }
        
        $userName = $user->first_name . ' ' . $user->last_name;
        
        // Log the deletion action before deletion
        LoggingService::logUserDeletion($user, Auth::user());
        
        $user->delete();
        
        // Check if this is an AJAX request
        $isAjax = $request->header('X-Requested-With') === 'XMLHttpRequest' || 
                  $request->header('Accept') === 'application/json' ||
                  $request->expectsJson();
        
        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => "User {$userName} has been deleted successfully!",
                'user_id' => $userId
            ]);
        }
        
        return back()->with('success', "User {$userName} has been deleted successfully!");
    }
}
