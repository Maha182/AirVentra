<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Warehouse Placement Error</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size:16px;">
    <h1>Warehouse Placement Alert</h1>
    <p><strong>Product:</strong> {{ $emailData['product']->title }}</p>
    <p><strong>Expected Location:</strong> {{ $emailData['correct_location'] }}</p>
    <p><strong>Scanned Location:</strong> {{ $emailData['wrong_location'] }}</p>
    <p>Please verify and take corrective action.</p>
</body>
</html>
