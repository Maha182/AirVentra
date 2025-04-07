<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Warehouse Inventory Alert</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size:16px;">
    <h1>Warehouse Inventory Alert</h1>

    <p><strong>Rack ID:</strong> {{ $emailData['location_id'] }}</p>
    <p><strong>Current Quantity:</strong> {{ $emailData['detected_capacity'] }}</p>
    <p><strong>Rack Capacity:</strong> {{ $emailData['rack_capacity'] }}</p>
    <p><strong>Status:</strong> 
        <span style="color: {{ $emailData['status'] == 'overfilled' ? 'red' : ($emailData['status'] == 'underfilled' ? 'orange' : 'green') }};">
            {{ ucfirst($emailData['status']) }}
        </span>
    </p>

    @if(!empty($emailData['expired_batches']))
        <h2 style="color: red;">⚠️ Expired Batches</h2>
        <ul>
            @foreach($emailData['expired_batches'] as $batch)
                <li>
                    <strong>Product:</strong> {{ $batch['product'] }} <br>
                    <strong>Barcode:</strong> {{ $batch['barcode'] }} <br>
                    <strong>Quantity:</strong> {{ $batch['quantity'] }} <br>
                    <strong>Expiry Date:</strong> {{ $batch['expiry_date'] }} <br>
                </li>
                <hr>
            @endforeach
        </ul>
    @endif

    @if(!empty($emailData['soon_to_expire_batches']))
        <h2 style="color: orange;">⚠️ Soon to Expire Batches (within 5 days)</h2>
        <ul>
            @foreach($emailData['soon_to_expire_batches'] as $batch)
                <li>
                    <strong>Product:</strong> {{ $batch['product'] }} <br>
                    <strong>Barcode:</strong> {{ $batch['barcode'] }} <br>
                    <strong>Quantity:</strong> {{ $batch['quantity'] }} <br>
                    <strong>Expiry Date:</strong> {{ $batch['expiry_date'] }} <br>
                </li>
                <hr>
            @endforeach
        </ul>
    @endif

    <p>Please review this rack and take the necessary action immediately.</p>
</body>
</html>
