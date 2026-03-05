<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffMotion;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $staff = $query->orderBy('name')->paginate(25)->appends($request->query());

        $stats = [
            'total' => Staff::count(),
            'active' => Staff::where('is_active', true)->count(),
        ];

        return view('staff.index', compact('staff', 'stats'));
    }

    public function motions(Request $request)
    {
        $query = StaffMotion::with('staff');

        if ($staffId = $request->get('staff_id')) {
            $query->where('staff_id', $staffId);
        }

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        $motions = $query->latest('action_date')->paginate(25)->appends($request->query());
        $staffList = Staff::orderBy('name')->get();

        return view('staff.motions', compact('motions', 'staffList'));
    }

    public function show(Staff $staff)
    {
        $staff->loadCount(['motions', 'tasks']);
        $recentMotions = $staff->motions()->latest('action_date')->take(20)->get();
        return view('staff.show', compact('staff', 'recentMotions'));
    }
}
