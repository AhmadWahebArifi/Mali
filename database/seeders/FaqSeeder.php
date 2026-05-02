<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'How do I add a new transaction?',
                'answer' => 'Click the "Add Income" or "Add Expense" button on the dashboard, fill in the details including amount, category, and account, then click save.',
                'category' => 'Getting Started',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'question' => 'What\'s the difference between accounts and categories?',
                'answer' => 'Accounts are where your money is stored (e.g., Cash on Hand, HesabPay). Categories help you track what you spend money on (e.g., Food, Transport, Entertainment).',
                'category' => 'Basics',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'question' => 'How do I view my financial reports?',
                'answer' => 'Navigate to the Reports section from the sidebar. You can see monthly summaries, category breakdowns, and yearly performance data.',
                'category' => 'Reports',
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'question' => 'Can I edit or delete transactions?',
                'answer' => 'Yes! Go to the Transactions page, find the transaction you want to modify, and click the edit or delete button in the actions column.',
                'category' => 'Transactions',
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'question' => 'How are the savings goals calculated?',
                'answer' => 'Savings goals are based on your total account balances. The progress bar shows how close you are to your target savings amount of $25,000.',
                'category' => 'Goals',
                'sort_order' => 5,
                'is_active' => true
            ],
            [
                'question' => 'Is my financial data secure?',
                'answer' => 'Yes! All data is encrypted and stored securely in your local database. Only you have access to your financial information.',
                'category' => 'Security',
                'sort_order' => 6,
                'is_active' => true
            ],
            [
                'question' => 'How do I export my transaction data?',
                'answer' => 'On the Transactions page, use the filters to select the data you want, then click the "Export CSV" button to download your data.',
                'category' => 'Data Management',
                'sort_order' => 7,
                'is_active' => true
            ],
            [
                'question' => 'What does the Net Cash Flow represent?',
                'answer' => 'Net Cash Flow shows the difference between your total income and expenses for the current month. A positive number means you saved money that month.',
                'category' => 'Reports',
                'sort_order' => 8,
                'is_active' => true
            ]
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
