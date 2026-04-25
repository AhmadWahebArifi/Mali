<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\ActivityLog;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Calculate real statistics
        $totalTransactions = Transaction::where('created_by', $user->id)->count();
        $accountsCreated = Account::where('user_id', $user->id)->count();
        $categoriesUsed = Transaction::where('created_by', $user->id)
            ->distinct('category_id')
            ->whereNotNull('category_id')
            ->count('category_id');
        
        $statistics = [
            'total_transactions' => $totalTransactions,
            'accounts_created' => $accountsCreated,
            'categories_used' => $categoriesUsed,
            'member_since' => $user->created_at
        ];
        
        return view('profile.index', compact('user', 'statistics'));
    }
    
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }
    
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
        
        // Update basic info
        $oldValues = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
        ];
        
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);
        
        $newValues = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
        ];
        
        // Log the profile update
        LoggingService::audit('update', $user, 'Profile updated', $oldValues, $newValues);
        
        // Update password if provided
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }
            $user->update(['password' => Hash::make($request->password)]);
            
            // Log password change activity
            ActivityLog::log(
                'password_changed',
                'security',
                'User changed their password',
                [
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]
            );
        }
        
        return redirect()->route('profile.index')->with('success', 'Profile updated successfully!');
    }
    
    public function exportData()
    {
        $user = auth()->user();
        
        // Get user's data
        $transactions = Transaction::where('created_by', $user->id)
            ->with(['account', 'category'])
            ->orderBy('date', 'desc')
            ->get();
            
        $accounts = Account::where('user_id', $user->id)->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_data_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($transactions, $accounts) {
            $file = fopen('php://output', 'w');
            
            // Accounts section
            fputcsv($file, ['ACCOUNTS']);
            fputcsv($file, ['Account Name', 'Balance', 'Created At']);
            
            foreach ($accounts as $account) {
                fputcsv($file, [
                    $account->name,
                    $account->balance,
                    $account->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fputcsv($file, []); // Empty row
            
            // Transactions section
            fputcsv($file, ['TRANSACTIONS']);
            fputcsv($file, ['Date', 'Description', 'Category', 'Type', 'Account', 'Amount', 'Created At']);
            
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->date->format('Y-m-d'),
                    $transaction->description,
                    $transaction->category ? $transaction->category->name : 'No Category',
                    ucfirst($transaction->type),
                    $transaction->account ? $transaction->account->name : 'No Account',
                    $transaction->amount,
                    $transaction->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    public function security()
    {
        $user = auth()->user();
        return view('profile.security', compact('user'));
    }
    
    public function help()
    {
        return view('profile.help');
    }
    
    public function loginActivity()
    {
        $user = auth()->user();
        
        // Get login activities for the current user
        $loginActivities = ActivityLog::where('user_id', $user->id)
            ->where('activity', 'login')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
            
        return response()->json([
            'activities' => $loginActivities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'ip_address' => $activity->ip_address,
                    'user_agent' => $activity->user_agent,
                    'description' => $activity->description,
                    'created_at' => $activity->created_at->format('M d, Y H:i:s'),
                    'diff_for_humans' => $activity->created_at->diffForHumans(),
                ];
            })
        ]);
    }
}
