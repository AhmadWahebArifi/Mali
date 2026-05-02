<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #004ccd;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #004ccd;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .filters {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .filters h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .filters p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #004ccd;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .income {
            color: #006c49;
            font-weight: bold;
        }
        .expense {
            color: #ba1a1a;
            font-weight: bold;
        }
        .summary {
            background: #f0f3ff;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            border-left: 4px solid #004ccd;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            color: #004ccd;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .summary-item {
            padding: 5px 0;
        }
        .summary-label {
            font-weight: bold;
            color: #333;
        }
        .summary-value {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Financial Report</h1>
        <p>Generated on {{ now()->format('F j, Y') }}</p>
    </div>

    <div class="filters">
        <h3>Report Filters</h3>
        <p><strong>Date Range:</strong> {{ \Carbon\Carbon::parse($filters['start_date'])->format('M j, Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('M j, Y') }}</p>
        @if($filters['category_id'])
            <p><strong>Category:</strong> {{ \App\Models\Category::find($filters['category_id'])->name }}</p>
        @endif
        @if($filters['account_id'])
            <p><strong>Account:</strong> {{ \App\Models\Account::find($filters['account_id'])->name }}</p>
        @endif
        @if($filters['type'])
            <p><strong>Type:</strong> {{ ucfirst($filters['type']) }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Category</th>
                <th>Account</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->date->format('M j, Y') }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ $transaction->category->name }}</td>
                    <td>{{ $transaction->account->name }}</td>
                    <td class="{{ $transaction->type }}">{{ ucfirst($transaction->type) }}</td>
                    <td class="{{ $transaction->_type }}">
                        @if($transaction->type == 'income')+@endif${{ number_format($transaction->amount, 2) }}
                    </td>
                    <td>${{ number_format($transaction->account->balance, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        No transactions found for the selected criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($transactions->count() > 0)
        <div class="summary">
            <h3>Summary</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <span class="summary-label">Total Transactions:</span>
                    <span class="summary-value">{{ $transactions->count() }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total Income:</span>
                    <span class="summary-value income">${{ number_format($transactions->where('type', 'income')->sum('amount'), 2) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total Expenses:</span>
                    <span class="summary-value expense">${{ number_format($transactions->where('type', 'expense')->sum('amount'), 2) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Net Cash Flow:</span>
                    <span class="summary-value">
                        @php
                            $netFlow = $transactions->where('type', 'income')->sum('amount') - $transactions->where('type', 'expense')->sum('amount');
                        @endphp
                        @if($netFlow >= 0)
                            <span class="income">+${{ number_format($netFlow, 2) }}</span>
                        @else
                            <span class="expense">-${{ number_format(abs($netFlow), 2) }}</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    @endif
</body>
</html>
