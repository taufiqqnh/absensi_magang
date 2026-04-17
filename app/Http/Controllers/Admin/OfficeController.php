<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\OfficeLocation;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function edit()
    {
        $office = Office::first();

        if (!$office) {
            $office = Office::create([
                'office_name' => 'Default Office',
                'address' => 'Belum tersedia',
            ]);
        }

        // pastikan location ada
        if (!$office->location) {
            OfficeLocation::create([
                'office_id' => $office->id,
                'latitude' => 0,
                'longitude' => 0,
                'radius' => 50,
            ]);
            $office->refresh();
        }

        return view('admin.offices.office', compact('office'));
    }

    public function update(Request $request)
    {
        $office = Office::firstOrFail();

        $request->validate([
            'office_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:0',
        ]);

        // update office
        $office->update($request->only(['office_name','address']));

        // update location
        if ($office->location) {
            $office->location->update([
                'latitude' => floatval($request->latitude),
                'longitude' => floatval($request->longitude),
                'radius' => floatval($request->radius),
            ]);
        } else {
            OfficeLocation::create([
                'office_id' => $office->id,
                'latitude' => floatval($request->latitude),
                'longitude' => floatval($request->longitude),
                'radius' => floatval($request->radius),
            ]);
        }

        return redirect()->route('office.edit')->with('success','Office dan lokasi berhasil diperbarui.');
    }
}
