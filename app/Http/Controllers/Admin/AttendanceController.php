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
            'face_image' => 'required|file|image|mimes:jpg,jpeg,png|max:2048',
            'face_descriptor' => 'nullable'
        ]);

        try {

            $faceImagePath = null;

            if ($request->hasFile('face_image')) {

                $file = $request->file('face_image');

                $fileName = 'faces/' . uniqid() . '_face.png';

                Storage::disk('public')->put(
                    $fileName,
                    file_get_contents($file)
                );

                $faceImagePath = $fileName;
            }

            FaceData::updateOrCreate(
                ['id_users' => $request->user_id],
                [
                    'face_image' => $faceImagePath,
                    'face_descriptor' => $request->face_descriptor ?? null
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Face saved successfully',
                'path' => $faceImagePath
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }



    public function getFaceData($user_id)
    {
        $face = FaceData::where('id_users', $user_id)->first();

        if (!$face) {
            return response()->json([
                'status' => false,
                'message' => 'Face not found'
            ]);
        }

        return response()->json([
            'status' => true,
            'face_descriptor' => $face->face_descriptor,
            'face_image' => $face->face_image
        ]);
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'office_id' => 'required|exists:tb_office,id',
            'device_name' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'nullable|string' // 🔥 TAMBAHAN
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
            // Ambil data kantor
            // =====================
            $office = Office::findOrFail($request->office_id);
            $officeLocation = $office->location;

            if (!$officeLocation) {
                return response()->json([
                    'status' => false,
                    'message' => 'Office location not set'
                ]);
            }

            $officeLat = (float) $officeLocation->latitude;
            $officeLng = (float) $officeLocation->longitude;
            $officeRadius = max((int) $officeLocation->radius, 5);

            $userLat = (float) $request->latitude;
            $userLng = (float) $request->longitude;

            $distance = $this->haversineDistance($userLat, $userLng, $officeLat, $officeLng);

            // =====================
            // Cek radius
            // =====================
            if ($distance > $officeRadius) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are out of office radius (' . round($distance, 2) . 'm)'
                ]);
            }

            // =====================
            // 🔥 SIMPAN IMAGE
            // =====================
            $imageName = null;

            if ($request->image) {
                $image = str_replace('data:image/png;base64,', '', $request->image);
                $image = str_replace(' ', '+', $image);

                $imageName = 'attendance_' . uniqid() . '.png';

                Storage::disk('public')->put(
                    'attendance/' . $imageName,
                    base64_decode($image)
                );
            }

            // =====================
            // Device
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
            // Status hadir / telat
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
            // INSERT DATA
            // =====================
            Attendance::create([
                'id_users' => $request->user_id,
                'office_id' => $request->office_id,
                'device_id' => $device_id,
                'latitude' => $userLat,
                'longitude' => $userLng,
                'attendance_date' => $today,
                'check_in' => $now->format('H:i:s'),
                'status' => $status,
                'image' => $imageName // 🔥 SIMPAN FOTO
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
                'message' => 'Check-in failed'
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
   public function indexAbsensidata(Request $request)
{
    $user = Auth::user();

    // Ambil role dinamis dari tabel user
    $roles = \App\Models\User::select('role')->distinct()->pluck('role');

    // Base query
    $query = Attendance::with(['user', 'office', 'device']);

    // Jika magang → hanya data sendiri
    if ($user->role == 'magang') {
        $query->where('id_users', $user->id);
    }

    // 🔥 FILTER ROLE (untuk admin/pimpinan)
    if ($request->role) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('role', $request->role);
        });
    }

    $attendances = $query->latest()->get();

    return view('admin.attendance.index', compact('attendances', 'roles'));
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
