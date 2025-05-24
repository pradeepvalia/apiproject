<!DOCTYPE html>
<html>
<head>
    <title>Donation Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .details { margin: 20px 0; }
        .amount { font-size: 24px; font-weight: bold; }
        .footer { margin-top: 50px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Donation Receipt</h1>
    </div>

    <div class="details">
        <p><strong>Donor Name:</strong> {{ $donation->full_name }}</p>
        <p><strong>Email:</strong> {{ $donation->email }}</p>
        <p><strong>Transaction ID:</strong> {{ $donation->transaction_id }}</p>
        <p><strong>Date:</strong> {{ $donation->created_at->format('F j, Y') }}</p>
        <p><strong>Payment Method:</strong> {{ ucfirst($donation->payment_method) }}</p>
        <p class="amount">Amount: ${{ number_format($donation->amount, 2) }}</p>
    </div>

    <div class="footer">
        <p>Thank you for your generous donation!</p>
    </div>
</body>
</html>
