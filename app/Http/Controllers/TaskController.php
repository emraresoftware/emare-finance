<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Staff;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with('assignedStaff');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }

        $tasks = $query->latest()->paginate(25)->appends($request->query());
        $staffList = Staff::orderBy('name')->get();

        $stats = [
            'total' => Task::count(),
            'pending' => Task::where('status', 'pending')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'completed' => Task::where('status', 'completed')->count(),
        ];

        return view('tasks.index', compact('tasks', 'staffList', 'stats'));
    }
}
