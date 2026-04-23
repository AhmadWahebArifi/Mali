<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // Mock notifications data
        $notifications = [
            [
                'id' => 1,
                'title' => 'New transaction added',
                'message' => 'Income of $1,500.00 has been added to Cash on Hand account',
                'type' => 'success',
                'time' => '2 minutes ago',
                'read' => false
            ],
            [
                'id' => 2,
                'title' => 'Account balance low',
                'message' => 'Your HesabPay account balance is below $100',
                'type' => 'warning',
                'time' => '1 hour ago',
                'read' => false
            ],
            [
                'id' => 3,
                'title' => 'Monthly report ready',
                'message' => 'Your April 2026 financial report is ready to view',
                'type' => 'info',
                'time' => '3 hours ago',
                'read' => true
            ],
            [
                'id' => 4,
                'title' => 'Budget exceeded',
                'message' => 'You have exceeded your Food & Dining budget by $200',
                'type' => 'error',
                'time' => '1 day ago',
                'read' => true
            ]
        ];
        
        return view('notifications.index', compact('notifications'));
    }
}
