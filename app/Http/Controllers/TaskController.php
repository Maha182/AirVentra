<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
class TaskController extends Controller
{
    // Display all tasks assigned to the authenticated user
    public function index(Request $request)
    {
        $query = Task::where('assigned_to', auth()->id());

        if ($request->has('status')) {
            if ($request->status === 'incomplete') {
                $query->where('status', '!=', 'completed');
            } elseif (in_array($request->status, ['pending', 'completed'])) {
                $query->where('status', $request->status);
            }
        }

        $tasks = $query->get();

        return view('tasks', compact('tasks'));
    }


    // API endpoint to fetch task statistics (for widget updates)
    

    public function getTaskStats()
    {
        try {
            $tasks = Task::where('assigned_to', auth()->id())->get();

            $taskStats = [
                'all_tasks' => $tasks->count(),
                'incomplete_tasks' => $tasks->where('status', '!=', 'completed')->count(),
                'overdue_tasks' => $tasks->filter(function ($task) {
                    return ($task->status !== 'completed' && Carbon::parse($task->deadline)->isPast()) ||
                           ($task->status === 'completed' && $task->completed_at && Carbon::parse($task->completed_at)->greaterThan(Carbon::parse($task->deadline)));
                })->count(),
                'due_today' => $tasks->filter(function ($task) {
                    return $task->status !== 'completed' && Carbon::parse($task->deadline)->toDateString() === Carbon::today()->toDateString();
                })->count(),
            ];

            return response()->json($taskStats);

        } catch (\Exception $e) {
            \Log::error('Error in getTaskStats: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }


    // Update task status via AJAX request
    public function markAsComplete(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->status = $request->status;
        $task->completed_at = ($request->status === 'completed') ? now() : null;
        $task->save();

        if ($task->status === 'completed') {
            if ($task->error_type === 'misplaced') {
                // Update placement_error_report
                DB::table('placement_error_report')
                    ->where('id', $task->error_id)
                    ->update(['status' => 'Corrected']);
            } elseif ($task->error_type === 'capacity') {
                // Update inventory_levels_report
                DB::table('inventory_levels_report')
                    ->where('id', $task->error_id)
                    ->update(['status' => 'normal']);
            }
        }

        return response()->json([
            'success' => true,
            'status' => $task->status
        ]);
    }


    public function getCompletedTasksTrend($filter)  // Get filter from the URL parameter
    {
        $userId = auth()->id();

        // Query to get the count of completed tasks by period
        $query = Task::where('assigned_to', $userId)
            ->where('status', 'completed') // Only completed tasks
            ->selectRaw("
                DATE_FORMAT(completed_at, " . ($filter === 'month' ? "'%Y-%m'" : ($filter === 'week' ? "'%Y-%u'" : "'%Y-%m-%d'")) . ") as period,
                COUNT(*) as completed
            ")
            ->groupBy('period')
            ->orderBy('period', 'ASC')
            ->get();

        // Prepare the data for the chart
        $dates = $query->pluck('period');
        $completed = $query->pluck('completed');

        return response()->json([
            'dates' => $dates,
            'completed' => $completed
        ]);
    }



    



    public function getTaskBreakdown(Request $request)
    {
        $userId = auth()->id();

        // Group tasks by status and count them
        $query = Task::where('assigned_to', $userId)
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->get();

        // Format data for Highcharts
        $data = $query->map(function ($item) {
            return [
                'name' => ucfirst($item->status), // Capitalize first letter
                'y' => (int) $item->count, // Convert to integer
                'color' => match (strtolower($item->status)) {
                    'pending' => '#f39c12', // Yellow
                    'in-progress' => '#3498db', // Blue
                    'completed' => '#2ecc71', // Green
                    default => '#95a5a6' // Gray for any other status
                }
            ];
        });

        return response()->json(['data' => $data]);
    }





    public function details($taskId)
    {
        $task = Task::findOrFail($taskId); // Find task or return 404
        $response = [
            'error_type' => $task->error_type, // Add this line to include the error_type
        ];

        if ($task->error_type == 'misplaced') {
            // Fetch misplaced product details from placement_error_report
            $placementError = DB::table('placement_error_report')
                                ->where('id', $task->error_id)
                                ->first();

            if ($placementError) {
                $response = array_merge($response, [
                    'type' => 'misplaced',
                    'wrong_location' => $placementError->wrong_location,
                    'correct_location' => $placementError->correct_location,
                ]);
            }
        } elseif ($task->error_type == 'capacity') {
            // Fetch stock capacity details from inventory_levels_report
            $inventoryLevel = DB::table('inventory_levels_report')
                                ->where('id', $task->error_id)
                                ->first();

            if ($inventoryLevel) {
                $response = array_merge($response, [
                    'type' => 'capacity',
                    'status' => $inventoryLevel->status,
                    'location_id' => $inventoryLevel->location_id,
                    'detected_capacity' => $inventoryLevel->detected_capacity,
                ]);
            }
        }

        return response()->json($response ?: ['error' => 'No relevant details found.']);
    }


}
