<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the user's profile form.
     */
    // public function index(Request $request): View
    // {
    //     return view('admin.index', [
    //         'user' => $request->user(),
    //     ]);
    // }

    public function index()
    {

        // $user = Auth::user();
        // Tanggal hari ini
        $today = Carbon::today();

        // Kehadiran hari ini
        $todayAttendance = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'hadir')
            ->count();

        // Terlambat hari ini
        $lateToday = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'telat')
            ->count();

        // Tidak hadir hari ini
        $absentToday = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'alpha')
            ->count();

        // Cuti / Izin hari ini
        $leaveToday = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'izin')
            ->count();

        // Total kehadiran sepanjang 30 hari terakhir
        $startDate = Carbon::today()->subDays(30);
        $totalAttendance = Attendance::whereBetween('attendance_date', [$startDate, $today])
            ->where('status', 'hadir')
            ->count();

        // Persentase untuk grafik / info tambahan
        $totalRecords = Attendance::whereBetween('attendance_date', [$startDate, $today])->count();
        $attendancePercentage = $totalRecords ? round(($totalAttendance / $totalRecords) * 100, 2) : 0;
        $latePercentage = $totalRecords ? round(($lateToday / $totalRecords) * 100, 2) : 0;
        $absentPercentage = $totalRecords ? round(($absentToday / $totalRecords) * 100, 2) : 0;
        $leavePercentage = $totalRecords ? round(($leaveToday / $totalRecords) * 100, 2) : 0;

        return view('admin.index', compact(
            'todayAttendance',
            'totalAttendance',
            'lateToday',
            'absentToday',
            'leaveToday',
            'attendancePercentage',
            'latePercentage',
            'absentPercentage',
            'leavePercentage'
        ));
    }
}
