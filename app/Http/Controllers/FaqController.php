<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = collect([
            [
                'question' => 'How do I add a new transaction?',
                'answer' => 'Click the "Add Income" or "Add Expense" button on the dashboard, fill in the details including amount, category, and account, then click save.',
                'category' => 'Getting Started'
            ],
            [
                'question' => 'What\'s the difference between accounts and categories?',
                'answer' => 'Accounts are where your money is stored (e.g., Cash on Hand, HesabPay). Categories help you track what you spend money on (e.g., Food, Transport, Entertainment).',
                'category' => 'Basics'
            ],
            [
                'question' => 'How do I view my financial reports?',
                'answer' => 'Navigate to the Reports section from the sidebar. You can see monthly summaries, category breakdowns, and yearly performance data.',
                'category' => 'Reports'
            ],
            [
                'question' => 'Can I edit or delete transactions?',
                'answer' => 'Yes! Go to the Transactions page, find the transaction you want to modify, and click the edit or delete button in the actions column.',
                'category' => 'Transactions'
            ],
            [
                'question' => 'How are the savings goals calculated?',
                'answer' => 'Savings goals are based on your total account balances. The progress bar shows how close you are to your target savings amount of $25,000.',
                'category' => 'Goals'
            ],
            [
                'question' => 'Is my financial data secure?',
                'answer' => 'Yes! All data is encrypted and stored securely in your local database. Only you have access to your financial information.',
                'category' => 'Security'
            ],
            [
                'question' => 'How do I export my transaction data?',
                'answer' => 'On the Transactions page, use the filters to select the data you want, then click the "Export CSV" button to download your data.',
                'category' => 'Data Management'
            ],
            [
                'question' => 'What does the Net Cash Flow represent?',
                'answer' => 'Net Cash Flow shows the difference between your total income and expenses for the current month. A positive number means you saved money that month.',
                'category' => 'Reports'
            ]
        ]);
        
        return view('faq.index', compact('faqs'));
    }
}
