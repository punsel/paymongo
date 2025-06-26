<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GCash Payment</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --gcash-green: #006241;
            --gcash-light-green: #00a86b;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .payment-container {
            max-width: 500px;
            margin: 30px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .payment-header {
            background: var(--gcash-green);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .payment-header h1 {
            font-size: 1.8rem;
            margin: 0;
            font-weight: 600;
        }
        
        .payment-header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        
        .payment-body {
            padding: 30px;
        }
        
        .amount-input {
            text-align: center;
            margin: 20px 0;
        }
        
        .amount-input label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 8px;
            display: block;
        }
        
        .amount-input .input-group {
            max-width: 300px;
            margin: 0 auto;
        }
        
        .amount-input .input-group-text {
            background: white;
            border-right: none;
            color: #666;
        }
        
        .amount-input .form-control {
            border-left: none;
            font-size: 1.5rem;
            font-weight: 600;
            text-align: right;
            padding-right: 15px;
        }
        
        .amount-input .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }
        
        .form-label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }
        
        .form-control {
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }
        
        .form-control:focus {
            border-color: var(--gcash-light-green);
            box-shadow: 0 0 0 0.2rem rgba(0, 168, 107, 0.25);
        }
        
        .btn-pay {
            background: var(--gcash-green);
            border: none;
            padding: 15px;
            font-weight: 600;
            width: 100%;
            border-radius: 10px;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        
        .btn-pay:hover {
            background: var(--gcash-light-green);
            transform: translateY(-2px);
        }
        
        .gcash-logo {
            width: 80px;
            margin-bottom: 15px;
        }
        
        .payment-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            font-size: 0.9rem;
            color: #666;
        }
        
        .payment-info i {
            color: var(--gcash-green);
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-container">
            <div class="payment-header">
                <img src="https://www.gcash.com/assets/images/gcash-logo-white.svg" alt="GCash Logo" class="gcash-logo">
                <h1>Send Money</h1>
                <p>Fast, safe, and convenient</p>
            </div>
            
            <div class="payment-body">
                <form action="create_payment.php" method="POST">
                    <div class="amount-input">
                        <label>Amount to Send</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" name="amount" id="amount" 
                                   min="1" step="0.01" required 
                                   placeholder="0.00" value="1000.00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="customerName" class="form-label">Recipient's Name</label>
                        <input type="text" class="form-control" id="customerName" name="customerName" 
                               required placeholder="Enter recipient's name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="customerEmail" class="form-label">Recipient's Email</label>
                        <input type="email" class="form-control" id="customerEmail" name="customerEmail" 
                               required placeholder="Enter recipient's email">
                    </div>

                    <div class="payment-info">
                        <p><i class="fas fa-shield-alt"></i> Your payment is secure and encrypted</p>
                        <p><i class="fas fa-bolt"></i> Instant transfer to recipient</p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-pay">
                        <i class="fas fa-paper-plane me-2"></i>Send Money
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Format amount input
        document.getElementById('amount').addEventListener('input', function(e) {
            let value = e.target.value;
            if (value < 0) e.target.value = 0;
        });
    </script>
</body>
</html> 