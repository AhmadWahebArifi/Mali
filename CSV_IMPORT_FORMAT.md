# CSV Import Format Guide

## Required Columns

Your CSV file must include the following columns (case-insensitive):

| Column | Required | Format | Description |
|--------|----------|---------|-------------|
| Date | Yes | YYYY-MM-DD | Transaction date |
| Description | Yes | Text | Description of the transaction |
| Amount | Yes | Number | Positive decimal number |
| Type | Yes | income/expense | Transaction type |
| Category | Yes | Text | Category name |
| Account | No | Text | Account name (optional) |

## Sample CSV Format

```csv
Date,Description,Amount,Type,Category,Account
2024-01-01,Monthly Salary,5000,income,Salary,Main Account
2024-01-02,Grocery Shopping,185.50,expense,Food & Dining,Main Account
```

## Field Requirements

### Date
- Supported formats: YYYY-MM-DD, MM/DD/YYYY, DD/MM/YYYY, MM-DD-YYYY, DD-MM-YYYY
- Examples: 2024-01-15, 01/15/2024, 15/01/2024, 01-15-2024, 15-01-2024
- Must be a valid date

### Description
- Any text description
- Cannot be empty

### Amount
- Positive decimal number
- No currency symbols (e.g., 123.45, not $123.45)
- Use decimal point for cents (e.g., 15.99)

### Type
- Must be exactly: `income` or `expense`
- Case-insensitive (INCOME, income, Income all work)

### Category
- Any category name
- If category doesn't exist, it will be created automatically
- Common categories: Salary, Food & Dining, Transportation, Bills & Utilities, etc.

### Account (Optional)
- Account name where transaction occurred
- If not specified, first available account will be used
- If account doesn't exist, it will be created for non-admin users

## Tips

1. **Save as CSV**: Make sure to save your file as CSV (.csv) format
2. **UTF-8 Encoding**: Use UTF-8 encoding for best compatibility
3. **No Headers in Data**: Only include headers in the first row
4. **Empty Rows**: Empty rows are automatically skipped
5. **Extra Columns**: Extra columns beyond the required ones are ignored

## Common Categories

### Income Categories
- Salary
- Business
- Investment
- Other Income

### Expense Categories
- Food & Dining
- Transportation
- Shopping
- Bills & Utilities
- Entertainment
- Healthcare
- Education
- Other Expenses

## Import Process

1. Go to Transactions page
2. Click "Import CSV" button (admin only)
3. Select your CSV file
4. Review import results
5. Success: Transactions are added to database
6. Errors: Check error messages for specific issues

## Error Troubleshooting

- **Invalid Date**: Use YYYY-MM-DD, MM/DD/YYYY, or DD/MM/YYYY format
- **Invalid Amount**: Use numbers only, no currency symbols
- **Invalid Type**: Use exactly "income" or "expense"
- **Missing Data**: Ensure all required columns have values
- **File Format**: Ensure file is saved as CSV, not Excel
