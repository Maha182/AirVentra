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
        <span style="color: {{ $emailData['status'] == 'overstock' ? 'red' : ($emailData['status'] == 'understock' ? 'orange' : 'green') }};">
            {{ ucfirst($emailData['status']) }}
        </span>
    </p>

    @if (!empty($emailData['product_alerts']))
        <h2>Product Alerts:</h2>
        <table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Status</th>
                    <th>Quantity</th>
                    <th>Stock Limit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($emailData['product_alerts'] as $alert)
                    <tr>
                        <td>{{ $alert['product_id'] }}</td>
                        <td>{{ $alert['name'] }}</td>
                        <td style="color: {{ $alert['status'] == 'overstock' ? 'red' : 'orange' }};">
                            {{ ucfirst($alert['status']) }}
                        </td>
                        <td>{{ $alert['quantity'] }}</td>
                        <td>{{ $alert['status'] == 'understock' ? 'Min: ' . $alert['min_stock'] : 'Max: ' . $alert['max_stock'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p>Please review this rack and take the necessary action.</p>
</body>
</html>