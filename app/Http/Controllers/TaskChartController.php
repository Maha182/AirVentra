<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TaskChartController extends Controller
{
    // Task Status Breakdown (Pie Chart)
    public function taskStatusBreakdown()
    {
        $tasks = DB::table('tasks')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'data' => $tasks->map(function($task) {
                return [
                    'name' => ucfirst($task->status),
                    'y' => $task->count,
                    // Color coding based on status
                    'color' => $task->status === 'pending' ? '#f39c12' : 
                              ($task->status === 'in-progress' ? '#3498db' : '#2ecc71')
                ];
            })
        ]);
    }

    // Task Completion Trend (Line Chart)
    public function taskCompletionTrend()
    {
        $tasks = DB::table('tasks')
            ->selectRaw('DATE(completed_at) as date, COUNT(*) as count')
            ->whereNotNull('completed_at')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'dates' => $tasks->pluck('date'),
            'counts' => $tasks->pluck('count')
        ]);
    }

    // Task Distribution by Employee (Bar Chart)
    public function taskDistributionByEmployee()
    {
        $tasks = DB::table('tasks')
            ->join('users', 'tasks.assigned_to', '=', 'users.id')
            ->selectRaw('users.first_name, COUNT(*) as count')
            ->groupBy('users.first_name')
            ->get();

        return response()->json([
            'employees' => $tasks->pluck('first_name'),
            'task_counts' => $tasks->pluck('count')
        ]);
    }

    // Live Task Updates (Gauge Chart)
    public function liveTaskUpdates()
    {
        $completed = DB::table('tasks')->where('status', 'completed')->count();
        $total = DB::table('tasks')->count();

        return response()->json([
            'completed' => $completed,
            'total_tasks' => $total
        ]);
    }

    // Task Types Distribution (Area Chart)
    public function taskTypesDistribution()
    {
        $tasks = DB::table('tasks')
            ->selectRaw('error_type as type, DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('error_type', 'date')
            ->orderBy('date')
            ->get();

        $grouped = $tasks->groupBy('type');
        $series = [];
        
        foreach ($grouped as $type => $items) {
            $series[] = [
                'name' => ucfirst($type),
                'data' => $items->pluck('count')
            ];
        }

        return response()->json([
            'dates' => $tasks->pluck('date')->unique()->values(),
            'series' => $series
        ]);
    }

    // Task Completion Time (Scatter Plot)
    public function taskCompletionTime()
    {
        $tasks = DB::table('tasks')
            ->join('users', 'tasks.assigned_to', '=', 'users.id')
            ->selectRaw('users.id as employee_id, users.first_name as employee, 
                    COALESCE(AVG(TIMESTAMPDIFF(MINUTE, assigned_at, completed_at)), 0) as avg_time')
            ->whereNotNull('completed_at')
            ->groupBy('users.id', 'users.first_name')
            ->get();

        return response()->json([
            'employees' => $tasks->pluck('employee'),
            'times' => $tasks->pluck('avg_time')->map(fn($time) => (float) $time) // Convert to float
        ]);
    }




    // Assigned vs Completed (Dual Axes Chart)
    public function assignedVsCompleted()
    {
        $assignedTasks = DB::table('tasks')
            ->selectRaw('DATE(assigned_at) as assigned_date, COUNT(*) as assigned')
            ->groupBy('assigned_date');

        $completedTasks = DB::table('tasks')
            ->selectRaw('DATE(completed_at) as completed_date, COUNT(*) as completed')
            ->whereNotNull('completed_at')
            ->groupBy('completed_date');

        $merged = DB::table(DB::raw("({$assignedTasks->toSql()}) as assignedTasks"))
            ->mergeBindings($assignedTasks)
            ->leftJoinSub($completedTasks, 'completedTasks', function ($join) {
                $join->on('assignedTasks.assigned_date', '=', 'completedTasks.completed_date');
            })
            ->orderBy('assignedTasks.assigned_date')
            ->get();

        return response()->json([
            'dates' => $merged->pluck('assigned_date'),
            'assigned' => $merged->pluck('assigned'),
            'completed' => $merged->pluck('completed')->map(fn($value) => $value ?? 0), // Avoid null values
        ]);
    }



    // Workload 3D (3D Column Chart)
    public function workload3D()
    {
        $tasks = DB::table('tasks')
            ->join('users', 'tasks.assigned_to', '=', 'users.id')
            ->selectRaw('users.first_name as employee, 
                    COALESCE(SUM(CASE WHEN tasks.status = "pending" THEN 1 ELSE 0 END), 0) as pending,
                    COALESCE(SUM(CASE WHEN tasks.status = "in-progress" THEN 1 ELSE 0 END), 0) as in_progress,
                    COALESCE(SUM(CASE WHEN tasks.status = "completed" THEN 1 ELSE 0 END), 0) as completed')
            ->groupBy('users.first_name')
            ->get();

        return response()->json([
            'employees' => $tasks->pluck('employee'),
            'pending' => $tasks->pluck('pending')->map(fn($val) => (int) $val),
            'in_progress' => $tasks->pluck('in_progress')->map(fn($val) => (int) $val),
            'completed' => $tasks->pluck('completed')->map(fn($val) => (int) $val)
        ]);
    }



    // Delayed vs On-Time (Bar Chart with Negative Values)
    public function delayedVsOnTime(Request $request)
    {
        $viewBy = $request->query('view_by', 'date');

        if ($viewBy === 'employee') {
            $tasks = DB::table('tasks')
                ->join('users', 'tasks.assigned_to', '=', 'users.id')
                ->selectRaw("
                    users.first_name as task_group,
                    SUM(CASE WHEN completed_at <= deadline THEN 1 ELSE 0 END) AS on_time,
                    SUM(CASE WHEN completed_at > deadline THEN 1 ELSE 0 END) * -1 AS delayed_tasks
                ")
                ->whereNotNull('completed_at')
                ->groupBy('users.first_name')
                ->orderBy('task_group', 'ASC')
                ->get();
        } else {
            $groupByColumn = match ($viewBy) {
                'month' => 'DATE_FORMAT(completed_at, "%Y-%m")',
                default => 'DATE(completed_at)'
            };

            $tasks = DB::table('tasks')
                ->selectRaw("
                    $groupByColumn as task_group,
                    SUM(CASE WHEN completed_at <= deadline THEN 1 ELSE 0 END) AS on_time,
                    SUM(CASE WHEN completed_at > deadline THEN 1 ELSE 0 END) * -1 AS delayed_tasks
                ")
                ->whereNotNull('completed_at')
                ->groupBy(DB::raw($groupByColumn))
                ->orderBy('task_group', 'ASC')
                ->get();
        }

        return response()->json([
            'categories' => $tasks->pluck('task_group'),
            'on_time' => $tasks->pluck('on_time')->map(fn($val) => (int) $val),
            'delayed' => $tasks->pluck('delayed_tasks')->map(fn($val) => (int) $val)
        ]);
    }



}