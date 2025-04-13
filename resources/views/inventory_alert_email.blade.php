<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Inventory Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            background-color: #f9f9f9;
            color: #333;
            padding: 20px;
        }

        h1 {
            color: #444;
        }

        h2 {
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .status-overfilled {
            color: red;
            font-weight: bold;
        }

        .status-underfilled {
            color: orange;
            font-weight: bold;
        }

    </style>
</head>
<body>

    <h1>📦 Warehouse Inventory Alert</h1>

    <p><strong>Rack ID:</strong> {{ $emailData['location_id'] }}</p>
    <p><strong>Current Quantity:</strong> {{ $emailData['detected_capacity'] }}</p>
    <p><strong>Rack Capacity:</strong> {{ $emailData['rack_capacity'] }}</p>
    <p><strong>Status:</strong> 
        <span class="status-{{ $emailData['status'] }}">
            {{ ucfirst($emailData['status']) }}
        </span>
    </p>

    @if(!empty($emailData['expired_batches']))
        <h2 style="color: red;">⚠️ Expired Batches</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Barcode</th>
                    <th>Quantity</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($emailData['expired_batches'] as $batch)
                    @php
                        $product = is_string($batch['product']) ? json_decode($batch['product'], true) : $batch['product'];
                    @endphp
                    <tr>
                        <td>{{ $product['id'] ?? 'N/A' }} - {{ $product['title'] ?? 'Unknown Product' }}</td>
                        <td>{{ $batch['barcode'] }}</td>
                        <td>{{ $batch['quantity'] }}</td>
                        <td>{{ $batch['expiry_date'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(!empty($emailData['soon_to_expire_batches']))
        <h2 style="color: orange;">⚠️ Soon to Expire Batches (within 5 days)</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Barcode</th>
                    <th>Quantity</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($emailData['soon_to_expire_batches'] as $batch)
                    @php
                        $product = is_string($batch['product']) ? json_decode($batch['product'], true) : $batch['product'];
                    @endphp
                    <tr>
                        <td>{{ $product['id'] ?? 'N/A' }} - {{ $product['title'] ?? 'Unknown Product' }}</td>
                        <td>{{ $batch['barcode'] }}</td>
                        <td>{{ $batch['quantity'] }}</td>
                        <td>{{ $batch['expiry_date'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p style="margin-top: 30px;">Please review this rack and take the necessary action immediately.</p>
    
</body>
</html>
