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
    <p><strong>Status:</strong> <span style="color: {{ $emailData['status'] == 'overstock' ? 'red' : 'orange' }};">
        {{ ucfirst($emailData['status']) }}
    </span></p>

    <p>Please review this rack and take the necessary action.</p>
</body>
</html>
