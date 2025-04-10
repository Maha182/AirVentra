<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskAssignmentController extends Controller
{
    public function assignTask($errorId)
    {
        // Find the employee with the least number of assigned tasks
        $employee = User::where('role', 'employee')
            ->withCount(['tasks' => function ($query) {
                $query->where('status', 'pending');
            }])
            ->orderBy('tasks_count', 'asc')
            ->first();

        if (!$employee) {
            return response()->json(['error' => 'No available employees found'], 404);
        }

        // Create the task with assigned_at timestamp
        $task = Task::create([
            'error_type' => 'misplaced',
            'error_id' => $errorId,
            'assigned_to' => $employee->id,
            'status' => 'pending',
            'deadline' => now()->addHours(4), // Example deadline
            'assigned_at' => now(), // Set assigned_at to the current timestamp
            'completed_at' => null, // Ensure completed_at is null initially
        ]);

        return $employee; // Return the created task with assigned details
    }
}
