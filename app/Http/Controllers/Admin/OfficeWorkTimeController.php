<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\OfficeWorkTime;
use Illuminate\Http\Request;

class OfficeWorkTimeController extends Controller
{
    public function edit()
    {
        // Ambil office pertama
        $office = Office::first();

        // Jika office belum ada, redirect ke menu office
        if (!$office) {
            return redirect()->route('office.edit') // sesuaikan route
                            ->with('error', 'Silakan buat office terlebih dahulu sebelum mengatur jam kerja.');
        }

        // Ambil work time office
        $workTime = $office->workTime;

        // Jika work time belum ada, buat default
        if (!$workTime) {
            $workTime = OfficeWorkTime::create([
                'office_id' => $office->id,
                'check_in_time' => '09:00',
                'check_out_time' => '17:00',
                'late_tolerance' => 0,
                'early_leave_tolerance' => 0,
            ]);
        }

        return view('admin.offices.worktime', compact('workTime'));
    }

    public function update(Request $request)
    {
        $office = Office::firstOrFail();
        $workTime = $office->workTime;

        $request->validate([
            'check_in_time' => 'required',
            'check_out_time' => 'required',
            'late_tolerance' => 'required|numeric|min:0',
            'early_leave_tolerance' => 'required|numeric|min:0',
        ]);

        if ($workTime) {
            $workTime->update($request->only([
                'check_in_time', 'check_out_time', 'late_tolerance', 'early_leave_tolerance'
            ]));
        } else {
            OfficeWorkTime::create(array_merge(
                $request->only([
                    'check_in_time', 'check_out_time', 'late_tolerance', 'early_leave_tolerance'
                ]),
                ['office_id' => $office->id]
            ));
        }

        return redirect()->route('office.worktime.edit')->with('success', 'Jam kerja office berhasil diperbarui.');
    }
}
