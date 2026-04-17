<x-admin.app>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Attendance Table</h4>
                        <p class="card-description">Daftar absensi semua user</p>

                        <form action="{{ route('attendance.export') }}" method="GET" class="mb-3">
                            <div class="row">

                                <!-- Filter Jenis -->
                                <div class="col-md-3">
                                    <select name="filter" class="form-control">
                                        <option value="">-- Pilih Filter --</option>
                                        <option value="harian">Harian</option>
                                        <option value="mingguan">Mingguan</option>
                                        <option value="bulanan">Bulanan</option>
                                        <option value="periode">Periode</option>
                                    </select>
                                </div>

                                <!-- Tanggal -->
                                <div class="col-md-2">
                                    <input type="date" name="start_date" class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <input type="date" name="end_date" class="form-control">
                                </div>

                                <!-- Role -->
                                <div class="col-md-2">
                                    <select name="role" class="form-control">
                                        <option value="">Semua Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="staff">Staff</option>
                                        <option value="magang">Magang</option>
                                    </select>
                                </div>

                                <!-- Button -->
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success w-100">
                                        Export Excel
                                    </button>
                                </div>

                            </div>
                        </form>

                        <!-- Alert Sukses / Error -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Table Absensi -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="attendance-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>User</th>
                                        <th>Office</th>
                                        <th>Tanggal</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendances as $index => $attendance)
                                        <tr data-latitude="{{ $attendance->latitude }}"
                                            data-longitude="{{ $attendance->longitude }}"
                                            data-device="{{ $attendance->device->device_name ?? '-' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $attendance->user->name ?? '-' }}</td>
                                            <td>{{ $attendance->office->office_name ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->translatedFormat('l, d-m-Y') }}
                                            </td>
                                            <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') : '-' }}
                                            </td>
                                            <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') : '-' }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge
                            @if ($attendance->status == 'hadir') badge-success
                            @elseif($attendance->status == 'telat') badge-warning
                            @elseif($attendance->status == 'izin' || $attendance->status == 'sakit') badge-info
                            @else badge-danger @endif">

                                                    @php
                                                        switch ($attendance->status) {
                                                            case 'telat':
                                                                echo 'Terlambat';
                                                                break;
                                                            case 'hadir':
                                                                echo 'Hadir';
                                                                break;
                                                            case 'izin':
                                                                echo 'Izin';
                                                                break;
                                                            case 'sakit':
                                                                echo 'Sakit';
                                                                break;
                                                            case 'alpha':
                                                            default:
                                                                echo 'Tidak Hadir';
                                                        }
                                                    @endphp

                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <!-- Tombol Detail -->
                                                    <button
                                                        class="btn btn-primary btn-sm rounded-circle viewAttendanceBtn"
                                                        style="width:35px;height:35px;padding:0"
                                                        data-id="{{ $attendance->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#viewAttendanceModal">
                                                        <i class="fa fa-eye"></i>
                                                    </button>

                                                    <!-- Tombol Delete -->
                                                    <form action="{{ route('attendance.destroy', $attendance->id) }}"
                                                        method="POST" class="deleteAttendanceForm">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-danger btn-sm rounded-circle"
                                                            style="width:35px;height:35px;padding:0">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Modal Detail Absensi -->
                        <div class="modal fade" id="viewAttendanceModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Absensi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>User</th>
                                                <td id="detail_user"></td>
                                            </tr>
                                            <tr>
                                                <th>Office</th>
                                                <td id="detail_office"></td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal</th>
                                                <td id="detail_date"></td>
                                            </tr>
                                            <tr>
                                                <th>Check-in</th>
                                                <td id="detail_checkin"></td>
                                            </tr>
                                            <tr>
                                                <th>Check-out</th>
                                                <td id="detail_checkout"></td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td id="detail_status"></td>
                                            </tr>
                                            <tr>
                                                <th>Latitude</th>
                                                <td id="detail_latitude"></td>
                                            </tr>
                                            <tr>
                                                <th>Longitude</th>
                                                <td id="detail_longitude"></td>
                                            </tr>
                                            <tr>
                                                <th>Device</th>
                                                <td id="detail_device"></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                $('#attendance-table').DataTable();

                // Konfirmasi delete
                $('#attendance-table').on('submit', '.deleteAttendanceForm', function(e) {
                    e.preventDefault();
                    let form = this;
                    Swal.fire({
                        title: 'Yakin hapus data absensi ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });

                // Tampilkan detail absensi di modal
                $('.viewAttendanceBtn').on('click', function() {
                    let row = $(this).closest('tr');
                    $('#detail_user').text(row.find('td:nth-child(2)').text());
                    $('#detail_office').text(row.find('td:nth-child(3)').text());
                    $('#detail_date').text(row.find('td:nth-child(4)').text());
                    $('#detail_checkin').text(row.find('td:nth-child(5)').text());
                    $('#detail_checkout').text(row.find('td:nth-child(6)').text());
                    $('#detail_status').text(row.find('td:nth-child(7) span').text());
                    $('#detail_latitude').text(row.data('latitude') || '-');
                    $('#detail_longitude').text(row.data('longitude') || '-');
                    $('#detail_device').text(row.data('device') || '-');
                });
            });
        </script>
    @endpush
</x-admin.app>
