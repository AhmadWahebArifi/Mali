<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionAlertMail;
use App\Mail\LowBalanceAlertMail;
use App\Mail\MonthlyReportMail;

class NotificationService
{
    /**
     * Create a notification for a user based on their preferences
     */
    public function createNotification($userId, $title, $message, $type = 'info', $data = [])
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        // Check if user has email notifications enabled
        $emailNotifications = Setting::getSetting($userId, 'email_notifications', true);
        
        // Create in-app notification
        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => json_encode($data),
            'is_read' => false,
        ]);

        // Send email if enabled
        if ($emailNotifications && $user->email) {
            $this->sendEmailNotification($user, $title, $message, $type, $data);
        }

        return $notification;
    }

    /**
     * Create transaction alert if user has it enabled
     */
    public function createTransactionAlert($transaction)
    {
        $user = User::find($transaction->user_id);
        if (!$user) {
            return false;
        }

        // Check if user has transaction alerts enabled
        $transactionAlerts = Setting::getSetting($user->id, 'transaction_alerts', true);
        
        if (!$transactionAlerts) {
            return false;
        }

        $title = 'New Transaction';
        $message = "A new {$transaction->type} transaction of {$transaction->amount} has been added to your account.";
        $type = $transaction->type === 'income' ? 'success' : 'warning';
        $data = [
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
            'type' => $transaction->type,
            'description' => $transaction->description,
            'account' => $transaction->account->name,
            'category' => $transaction->category->name,
        ];

        return $this->createNotification($user->id, $title, $message, $type, $data);
    }

    /**
     * Create low balance alert if user has it enabled
     */
    public function createLowBalanceAlert($account)
    {
        $user = User::find($account->user_id);
        if (!$user) {
            return false;
        }

        // Check if user has low balance alerts enabled
        $lowBalanceAlerts = Setting::getSetting($user->id, 'low_balance_alerts', true);
        
        if (!$lowBalanceAlerts) {
            return false;
        }

        // Check if balance is below threshold (let's say 100)
        $threshold = 100;
        if ($account->balance > $threshold) {
            return false;
        }

        $title = 'Low Balance Alert';
        $message = "Your account '{$account->name}' has a low balance of {$account->balance}.";
        $type = 'error';
        $data = [
            'account_id' => $account->id,
            'account_name' => $account->name,
            'balance' => $account->balance,
            'threshold' => $threshold,
        ];

        return $this->createNotification($user->id, $title, $message, $type, $data);
    }

    /**
     * Create monthly report if user has it enabled
     */
    public function createMonthlyReport($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        // Check if user has monthly reports enabled
        $monthlyReports = Setting::getSetting($userId, 'monthly_reports', true);
        
        if (!$monthlyReports) {
            return false;
        }

        // Generate monthly report data
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
        
        $transactions = Transaction::where('created_by', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpenses = $transactions->where('type', 'expense')->sum('amount');
        $netCashFlow = $totalIncome - $totalExpenses;
        $transactionCount = $transactions->count();

        $title = 'Monthly Financial Report';
        $message = "Your monthly report is ready. Income: {$totalIncome}, Expenses: {$totalExpenses}, Net: {$netCashFlow}";
        $type = 'info';
        $data = [
            'period' => now()->format('F Y'),
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_cash_flow' => $netCashFlow,
            'transaction_count' => $transactionCount,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];

        return $this->createNotification($userId, $title, $message, $type, $data);
    }

    /**
     * Check all accounts for low balance and create alerts
     */
    public function checkLowBalanceAlerts()
    {
        $accounts = Account::with('user')->get();
        $alertsCreated = 0;

        foreach ($accounts as $account) {
            if ($this->createLowBalanceAlert($account)) {
                $alertsCreated++;
            }
        }

        return $alertsCreated;
    }

    /**
     * Send monthly reports to all users who have them enabled
     */
    public function sendMonthlyReports()
    {
        $users = User::all();
        $reportsSent = 0;

        foreach ($users as $user) {
            if ($this->createMonthlyReport($user->id)) {
                $reportsSent++;
            }
        }

        return $reportsSent;
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification($user, $title, $message, $type, $data)
    {
        try {
            // For now, we'll just log the email (in a real app, you'd send actual emails)
            \Log::info("Email notification sent to {$user->email}: {$title} - {$message}");
            
            // In a real implementation, you would send actual emails like this:
            // switch ($type) {
            //     case 'success':
            //     case 'warning':
            //         Mail::to($user->email)->send(new TransactionAlertMail($title, $message, $data));
            //         break;
            //     case 'error':
            //         Mail::to($user->email)->send(new LowBalanceAlertMail($title, $message, $data));
            //         break;
            //     case 'info':
            //         Mail::to($user->email)->send(new MonthlyReportMail($title, $message, $data));
            //         break;
            // }
            
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to send email notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadNotifications($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->update(['is_read' => true]);
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->update(['is_read' => true]);
    }
}
