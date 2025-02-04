<?php

namespace App\Http\Controllers;
// app/Http/Controllers/YourController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class YourController extends Controller
{
    public function receiveData(Request $request)
    {
        $data = $request->all(); // Get all the data sent
        // Handle the received data (e.g., store it in the database)
        return response()->json(['message' => 'Data received successfully!', 'data' => $data]);
    }
}
