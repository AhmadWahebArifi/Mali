<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ActivityLog;

class LoggingService
{
    /**
     * Log an audit trail entry
     */
    public static function audit($action, $subject = null, $description = null, $oldValues = null, $newValues = null)
    {
        return AuditLog::log($action, $subject, $description, $oldValues, $newValues);
    }

    /**
     * Log an activity entry
     */
    public static function activity($activity, $module = null, $description = null, $metadata = null)
    {
        return ActivityLog::log($activity, $module, $description, $metadata);
    }

    /**
     * Log user login
     */
    public static function logLogin($user)
    {
        return self::activity('login', 'auth', "User {$user->email} logged in", [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Log user logout
     */
    public static function logLogout($user)
    {
        return self::activity('logout', 'auth', "User {$user->email} logged out", [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Log user registration
     */
    public static function logRegistration($user)
    {
        return self::audit('create', $user, "New user registration: {$user->email}", null, [
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
            'is_approved' => $user->is_approved,
        ]);
    }

    /**
     * Log user approval
     */
    public static function logUserApproval($user, $approvedBy)
    {
        return self::audit('approve', $user, "User {$user->email} approved by {$approvedBy->email}", [
            'is_approved' => false,
        ], [
            'is_approved' => true,
            'approved_by' => $approvedBy->id,
            'approved_at' => now(),
        ]);
    }

    /**
     * Log user rejection/deletion
     */
    public static function logUserRejection($user, $rejectedBy)
    {
        return self::audit('reject', $user, "User {$user->email} rejected by {$rejectedBy->email}", [
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
        ], null);
    }

    /**
     * Log user deletion
     */
    public static function logUserDeletion($user, $deletedBy)
    {
        return self::audit('delete', $user, "User {$user->email} deleted by {$deletedBy->email}", [
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
        ], null);
    }

    /**
     * Log transaction creation
     */
    public static function logTransactionCreate($transaction)
    {
        return self::audit('create', $transaction, "Transaction created: {$transaction->type} of {$transaction->amount}", null, [
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'account_id' => $transaction->account_id,
            'category_id' => $transaction->category_id,
            'description' => $transaction->description,
        ]);
    }

    /**
     * Log transaction update
     */
    public static function logTransactionUpdate($transaction, $oldValues, $newValues)
    {
        return self::audit('update', $transaction, "Transaction updated: ID {$transaction->id}", $oldValues, $newValues);
    }

    /**
     * Log transaction deletion
     */
    public static function logTransactionDelete($transaction)
    {
        return self::audit('delete', $transaction, "Transaction deleted: ID {$transaction->id}", [
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'account_id' => $transaction->account_id,
            'category_id' => $transaction->category_id,
            'description' => $transaction->description,
        ], null);
    }

    /**
     * Log account creation
     */
    public static function logAccountCreate($account)
    {
        return self::audit('create', $account, "Account created: {$account->name}", null, [
            'name' => $account->name,
            'balance' => $account->balance,
            'user_id' => $account->user_id,
        ]);
    }

    /**
     * Log account update
     */
    public static function logAccountUpdate($account, $oldValues, $newValues)
    {
        return self::audit('update', $account, "Account updated: {$account->name}", $oldValues, $newValues);
    }

    /**
     * Log account deletion
     */
    public static function logAccountDelete($account)
    {
        return self::audit('delete', $account, "Account deleted: {$account->name}", [
            'name' => $account->name,
            'balance' => $account->balance,
        ], null);
    }

    /**
     * Log settings update
     */
    public static function logSettingsUpdate($userId, $settings)
    {
        return self::activity('update_settings', 'settings', "Settings updated for user ID: {$userId}", $settings);
    }

    /**
     * Log page view
     */
    public static function logPageView($page, $description = null)
    {
        return self::activity('view_page', 'navigation', $description ?? "Viewed page: {$page}", [
            'page' => $page,
            'timestamp' => now(),
        ]);
    }

    /**
     * Log export action
     */
    public static function logExport($type, $format, $metadata = null)
    {
        return self::activity('export', 'reports', "Exported {$type} as {$format}", array_merge([
            'type' => $type,
            'format' => $format,
            'timestamp' => now(),
        ], $metadata ?? []));
    }

    /**
     * Log notification sent
     */
    public static function logNotificationSent($userId, $type, $title)
    {
        return self::activity('notification_sent', 'notifications', "Notification sent: {$title}", [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
        ]);
    }
}
