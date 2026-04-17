<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($item, $index) {
            return [
                'No' => $index + 1,
                'User' => $item->user->name ?? '-',
                'Role' => $item->user->role ?? '-',
                'Office' => $item->office->office_name ?? '-',
                'Tanggal' => $item->attendance_date,
                'Check In' => $item->check_in,
                'Check Out' => $item->check_out,
                'Status' => $item->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'User',
            'Role',
            'Office',
            'Tanggal',
            'Check In',
            'Check Out',
            'Status'
        ];
    }
}
