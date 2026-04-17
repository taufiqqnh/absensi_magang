<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Device;
use App\Models\FaceData;
use App\Models\Office;
use App\Models\OfficeWorkTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    // ================================
    // REGISTER FACE DATA
    // ================================
    public function storeFace(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'face_descriptor' => 'required|string',
            // 'face_image' dihapus dari validasi karena Base64
        ]);

        try {
            $faceImagePath = null;

            if ($request->face_image) {
                $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $request->face_image);
                $imageData = base64_decode($imageData);

                $fileName = 'faces/' . uniqid() . '_face.png';
                Storage::disk('public')->put($fileName, $imageData);
                $faceImagePath = $fileName;
            }

            FaceData::updateOrCreate(
                ['id_users' => $request->user_id],
                [
                    'face_descriptor' => $request->face_descriptor,
                    'face_image' => $faceImagePath
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Face data saved successfully',
                'path' => $faceImagePath
            ]);
        } catch (\Exception $e) {
            Log::error('Face data save failed: '.$e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to save face data',
                'error' => $e->getMessage()
            ]);
        }
    }

    // ================================
    // CHECK IN
    // ================================
    // public function checkIn(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'office_id' => 'required|exists:tb_office,id',
    //         'device_name' => 'nullable|string',
    //         'latitude' => 'required|numeric',
    //         'longitude' => 'required|numeric'
    //     ]);

    //     $today = Carbon::today('Asia/Jakarta')->toDateString();

    //     try {
    //         // =====================
    //         // Cek absensi hari ini
    //         // =====================
    //         $attendance = Attendance::where('id_users', $request->user_id)
    //             ->where('attendance_date', $today)
    //             ->first();

    //         if ($attendance) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Already checked in today'
    //             ]);
    //         }

    //         // =====================
    //         // Simpan / update device
    //         // =====================
    //         $device = null;
    //         $deviceName = $request->device_name ? substr($request->device_name, 0, 255) : null; // aman untuk DB

    //         if ($deviceName) {
    //             $device = Device::updateOrCreate(
    //                 ['user_id' => $request->user_id, 'device_name' => $deviceName],
    //                 ['ip_address' => $request->ip()]
    //             );
    //         }

    //         $device_id = $device ? $device->id : null;

    //         // =====================
    //         // Tentukan status hadir/telat
    //         // =====================
    //         $workTime = OfficeWorkTime::where('office_id', $request->office_id)->first();
    //         $now = Carbon::now('Asia/Jakarta');

    //         if ($workTime) {
    //             $checkInTime = Carbon::parse($workTime->check_in_time, 'Asia/Jakarta');
    //             $lateLimit = $checkInTime->copy()->addMinutes($workTime->late_tolerance ?? 0);
    //             $status = $now->greaterThan($lateLimit) ? 'telat' : 'hadir';
    //         } else {
    //             $status = 'hadir';
    //         }

    //         // =====================
    //         // Logging untuk debugging
    //         // =====================
    //         Log::info('Check-in data', [
    //             'user_id' => $request->user_id,
    //             'office_id' => $request->office_id,
    //             'device_id' => $device_id,
    //             'latitude' => $request->latitude,
    //             'longitude' => $request->longitude,
    //             'today' => $today,
    //             'status' => $status,
    //             'current_time' => $now->toDateTimeString(),
    //             'check_in_time' => $workTime->check_in_time ?? null,
    //             'late_tolerance' => $workTime->late_tolerance ?? null
    //         ]);

    //         // =====================
    //         // Insert attendance
    //         // =====================
    //         Attendance::create([
    //             'id_users' => $request->user_id,
    //             'office_id' => $request->office_id,
    //             'device_id' => $device_id,
    //             'latitude' => (float)$request->latitude,
    //             'longitude' => (float)$request->longitude,
    //             'attendance_date' => $today,
    //             'check_in' => $now->format('H:i:s'),
    //             'status' => $status
    //         ]);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Check-in successful',
    //             'attendance_status' => $status
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error('Check-in failed: '.$e->getMessage(), ['request' => $request->all()]);
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Check-in failed, see log'
    //         ]);
    //     }
    // }

    public function checkIn(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'office_id' => 'required|exists:tb_office,id',
            'device_name' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $today = Carbon::today('Asia/Jakarta')->toDateString();

        try {
            // =====================
            // Cek absensi hari ini
            // =====================
            $attendance = Attendance::where('id_users', $request->user_id)
                ->where('attendance_date', $today)
                ->first();

            if ($attendance) {
                return response()->json([
                    'status' => false,
                    'message' => 'Already checked in today'
                ]);
            }

            // =====================
            // Ambil data kantor & location
            // =====================
            $office = Office::findOrFail($request->office_id);
            $officeLocation = $office->location; // pastikan relasi hasOne di Office.php

            if (!$officeLocation) {
                return response()->json([
                    'status' => false,
                    'message' => 'Office location not set'
                ]);
            }

            $officeLat = (float) $officeLocation->latitude;
            $officeLng = (float) $officeLocation->longitude;
            $officeRadius = max((int) $officeLocation->radius, 5); // minimal 5 m

            // =====================
            // Hitung jarak Haversine
            // =====================
            $userLat = (float) $request->latitude;
            $userLng = (float) $request->longitude;

            $distance = $this->haversineDistance($userLat, $userLng, $officeLat, $officeLng);

            // =====================
            // Debug log
            // =====================
            Log::info('Check-in debug', [
                'userLat' => $userLat,
                'userLng' => $userLng,
                'officeLat' => $officeLat,
                'officeLng' => $officeLng,
                'distance' => $distance,
                'officeRadius' => $officeRadius
            ]);

            // =====================
            // Cek radius
            // =====================
            if ($distance > $officeRadius) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are out of office radius (' . round($distance, 2) . 'm > ' . $officeRadius . 'm)'
                ]);
            }

            // =====================
            // Simpan / update device
            // =====================
            $device = null;
            $deviceName = $request->device_name ? substr($request->device_name, 0, 255) : null;

            if ($deviceName) {
                $device = Device::updateOrCreate(
                    ['user_id' => $request->user_id, 'device_name' => $deviceName],
                    ['ip_address' => $request->ip()]
                );
            }

            $device_id = $device ? $device->id : null;

            // =====================
            // Tentukan status hadir/telat
            // =====================
            $workTime = OfficeWorkTime::where('office_id', $request->office_id)->first();
            $now = Carbon::now('Asia/Jakarta');

            $status = 'hadir';
            if ($workTime) {
                $checkInTime = Carbon::parse($workTime->check_in_time, 'Asia/Jakarta');
                $lateLimit = $checkInTime->copy()->addMinutes($workTime->late_tolerance ?? 0);
                $status = $now->greaterThan($lateLimit) ? 'telat' : 'hadir';
            }

            // =====================
            // Insert attendance
            // =====================
            Attendance::create([
                'id_users' => $request->user_id,
                'office_id' => $request->office_id,
                'device_id' => $device_id,
                'latitude' => $userLat,
                'longitude' => $userLng,
                'attendance_date' => $today,
                'check_in' => $now->format('H:i:s'),
                'status' => $status
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Check-in successful',
                'attendance_status' => $status,
                'distance' => round($distance, 2)
            ]);

        } catch (\Exception $e) {
            Log::error('Check-in failed: '.$e->getMessage(), ['request' => $request->all()]);
            return response()->json([
                'status' => false,
                'message' => 'Check-in failed, see log'
            ]);
        }
    }

    /**
     * Fungsi Haversine - hitung jarak antara 2 koordinat (meter)
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    // ===================== CHECK OUT =====================
    public function checkOut(Request $request)
    {
        $request->validate([
            'user_id'=>'required|exists:users,id',
            'device_name'=>'nullable|string',
            'latitude'=>'required|numeric',
            'longitude'=>'required|numeric'
        ]);

        $today = Carbon::today()->toDateString();

        try {
            $attendance = Attendance::where('id_users', $request->user_id)
                ->where('attendance_date', $today)
                ->first();

            if(!$attendance){
                return response()->json([
                    'status'=>false,
                    'message'=>'You have not checked in yet'
                ]);
            }

            if($attendance->check_out){
                return response()->json([
                    'status'=>false,
                    'message'=>'Already checked out'
                ]);
            }

            // Simpan/update device
            $device = null;
            if($request->device_name){
                $device = Device::updateOrCreate(
                    ['user_id'=>$request->user_id,'device_name'=>$request->device_name],
                    ['ip_address'=>$request->ip()]
                );
            }

            $attendance->update([
                'device_id'=>$device ? $device->id : $attendance->device_id,
                'latitude'=>(float)$request->latitude,
                'longitude'=>(float)$request->longitude,
                'check_out'=>Carbon::now()->format('H:i:s')
            ]);

            return response()->json(['status'=>true,'message'=>'Check-out successful']);

        } catch (\Exception $e) {
            Log::error('Check-out failed: '.$e->getMessage(), ['request' => $request->all()]);
            return response()->json([
                'status'=>false,
                'message'=>'Check-out failed, see log'
            ]);
        }
    }

    // ================================
    // GET TODAY ATTENDANCE
    // ================================
    public function todayAttendance($user_id)
    {
        $today = Carbon::today()->toDateString();

        try {
            $attendance = Attendance::where('id_users', $user_id)
                ->where('attendance_date', $today)
                ->first();

            if (!$attendance) {
                return response()->json([
                    'status' => false,
                    'message' => 'No attendance record for today'
                ]);
            }

            return response()->json([
                'status' => true,
                'attendance' => $attendance
            ]);
        } catch (\Exception $e) {
            Log::error('Get today attendance failed: '.$e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to get today attendance'
            ]);
        }
    }

    // ==============================
    // HALAMAN DATA ABSENSI
    // ==============================
    public function indexAbsensidata()
    {
        $user = Auth::user();

        // Cek jika role magang
        if ($user->role == 'magang') {
            $attendances = Attendance::with(['user', 'office', 'device'])
                ->where('id_users', $user->id)
                ->latest()
                ->get();
        } else {
            $attendances = Attendance::with(['user', 'office', 'device'])
                ->latest()
                ->get();
        }

        return view('admin.attendance.index', compact('attendances'));
    }

    // ==============================
    // DELETE ABSENSI
    // ==============================
    public function destroy($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->delete();

            return redirect()->route('attendance.index')->with('success', 'Data absensi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('attendance.index')->with('error', 'Gagal menghapus data absensi');
        }
    }

    // ==============================
    // EXPORT ABSENSI
    // ==============================
    public function export(Request $request)
    {
        $query = Attendance::with(['user', 'office', 'device']);

        // 🔹 Filter Role
        if ($request->role) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        // 🔹 Filter Tanggal
        if ($request->filter == 'harian') {
            $query->whereDate('attendance_date', Carbon::today());
        }

        if ($request->filter == 'mingguan') {
            $query->whereBetween('attendance_date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        }

        if ($request->filter == 'bulanan') {
            $query->whereMonth('attendance_date', Carbon::now()->month)
                ->whereYear('attendance_date', Carbon::now()->year);
        }

        if ($request->filter == 'periode') {
            $query->whereBetween('attendance_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // 🔥 Khusus MAGANG → hanya data sendiri
        // 🔥 Khusus MAGANG → hanya data sendiri
        if (Auth::check() && Auth::user()->role == 'magang') {
            $query->where('user_id', Auth::id());
        }

        $data = $query->get();

         return Excel::download(new AttendanceExport($data), 'laporan-absensi.xlsx');
    }

}
