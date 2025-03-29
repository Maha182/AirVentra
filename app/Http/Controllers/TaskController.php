<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {
        // Get tasks assigned to the logged-in user (My Tasks)
        $tasks = Task::where('assigned_to', auth()->id())->get();

        // Get tasks assigned to other employees (Employee Tasks)
        $employeeTasks = Task::where('assigned_to', '!=', auth()->id())->get();

        // Merge the tasks assigned to the logged-in user and other employees
        $allTasks = $tasks->merge($employeeTasks);

        // Pass both the tasks and the combined tasks to the view
        return view('tasks', compact('tasks', 'allTasks'));
    }

    public function markAsComplete($id)
    {
        $task = Task::findOrFail($id); // Find the task by ID
        $task->status = 'complete';    // Change the status to 'complete'
        $task->save();                 // Save the updated task

        return response()->json(['success' => true]);  // Return a JSON response
    }

    public function updateComment(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->comment = $request->comment;
        $task->save();

        return response()->json(['success' => true]);
    }

    /**
     * Fetch task details including misplaced information and stock capacity.
     */
    public function details($taskId)
    {
        // Fetch the task based on its ID
        $task = Task::findOrFail($taskId);
        $response = [];

        // Check if the task's error type is "misplaced"
        if ($task->error_type == 'misplaced') {
            // Fetch the placement error report details based on the task's error_id (which relates to product_id in placement_error_report)
            $placementError = DB::table('placement_error_report')
                                ->where('product_id', $task->error_id) // We use error_id to get the product_id
                                ->first();

            if ($placementError) {
                $response['wrong_location'] = $placementError->wrong_location;
                $response['correct_location'] = $placementError->correct_location;
            }
        }

        // Fetch stock capacity details
        $inventoryLevel = DB::table('inventory_levels_report')
                            ->where('product_id', $task->error_id)
                            ->first();

        if ($inventoryLevel) {
            $response['status'] = $inventoryLevel->capacity_status; // Assuming 'capacity_status' stores 'understock' or 'overstock'
            $response['location_id'] = $inventoryLevel->location_id;
        }

        return response()->json($response ?: ['error' => 'No relevant details found.']);
    }
}

