<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('tasks', compact('tasks'));
    }

    public function markAsComplete($id)
    {
        $task = Task::findOrFail($id);
        $task->status = 'complete';
        $task->save();
        return redirect()->back();
    }
}


