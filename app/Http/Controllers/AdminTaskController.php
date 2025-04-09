<?php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminTaskController extends Controller
{
    // Display all tasks for all employees with filtering options
    public function index(Request $request)
    {
        // Fetch all tasks (for widgets and charts)
        $tasks = Task::with('user')->get();
    
        // Fetch non-completed tasks (for the table only)
        $incompleteTasks = $tasks->where('status', '!=', 'completed');
    
        // Fetch employees only (assuming 'role' column)
        $employees = User::where('role', 'employee')->get();
    
        // Filter completed tasks for the completion trend chart
        $completedTasks = Task::with('user')->where('status', 'completed')->get();
    
        // Group completed tasks by completion date for the line chart
        $completedGrouped = $completedTasks->groupBy(function ($task) {
            return Carbon::parse($task->completed_at)->format('Y-m-d');
        });
    
        $completionDates = $completedGrouped->keys()->toArray();
        $completionCounts = $completedGrouped->map(function ($group) {
            return $group->count();
        })->values()->toArray();
    
        // Count overdue tasks
        $overdueTasks = $tasks->filter(function ($task) {
            if (!$task->deadline) return false;
            $deadline = Carbon::parse($task->deadline);
            return ($task->status !== 'completed' && $deadline->isPast()) ||
                   ($task->status === 'completed' && $task->completed_at && Carbon::parse($task->completed_at)->gt($deadline));
        });
    
        // Tasks due today (not completed)
        $dueTodayTasks = $tasks->filter(function ($task) {
            return $task->status !== 'completed' &&
                   $task->deadline &&
                   Carbon::parse($task->deadline)->toDateString() === Carbon::today()->toDateString();
        });
        $pendingTasks = $tasks->filter(function ($task) {
            return $task->status !== 'completed' && !in_array($task->status, ['overdue']);
        });
    
        // Return data to Blade view
        return view('admintasks2', [
            'tasks' => $incompleteTasks,  // Pass only non-completed tasks for the table
            'employees' => $employees,
            'completionDates' => $completionDates,  // Added for the chart
            'completionCounts' => $completionCounts, // Added for the chart
            'totalTasks' => $tasks->count(),
            'completedTasks' => $completedTasks->count(),
            'incompleteTasks' => $incompleteTasks->count(),
            'overdueTasks' => $overdueTasks->count(),
            'dueTodayTasks' => $dueTodayTasks->count(),
            'pendingTasks' => $pendingTasks->count(), // Add this to pass pendingTasks

        ]);
    }
    
    
    public function reassignTask(Request $request, $taskId)
    {
        \Log::info("Reassigning Task ID: $taskId to User ID: {$request->new_user_id}");
    
        // Validate the new user ID
        $validated = $request->validate([
            'new_user_id' => 'required|exists:users,id',
        ]);
    
        // Find the task to reassign
        $task = Task::find($taskId);
        
        if (!$task) {
            \Log::error("Task with ID $taskId not found.");
            return response()->json(['error' => 'Task not found'], 404);
        }
    
        // Find the user to reassign to
        $user = User::find($validated['new_user_id']);
        if (!$user) {
            \Log::error("User with ID {$validated['new_user_id']} not found.");
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Update the task with the new user ID
        $task->assigned_to = $validated['new_user_id'];
        $task->save();
    
        // Return the updated assignee's name for the frontend to update the table
        return response()->json([
            'success' => true,
            'new_assignee' => $task->user->first_name . ' ' . $task->user->last_name
        ]);
    }
    
    public function updateDeadline(Request $request, $id)
    {
        $request->validate([
            'deadline' => 'required|date'
        ]);

        $task = Task::findOrFail($id);
        $task->deadline = Carbon::parse($request->deadline);
        $task->save();

        return response()->json(['success' => true]);
    }


}

