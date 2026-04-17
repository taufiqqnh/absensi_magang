<x-admin.app>
    <div class="content-wrapper">
        <!-- Greeting / Header -->
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Welcome {{ Auth::user()->name }}</h3>
                        <h6 class="font-weight-normal mb-0">
                            Sistem Absensi Peserta Magang! You have {{ $todayAttendance }} attendances today.
                        </h6>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
                            <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button"
                                        id="dropdownMenuDate2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="mdi mdi-calendar"></i> Today ({{ \Carbon\Carbon::now()->translatedFormat('d M Y') }})
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                                    <a class="dropdown-item" href="#">January - March</a>
                                    <a class="dropdown-item" href="#">March - June</a>
                                    <a class="dropdown-item" href="#">June - August</a>
                                    <a class="dropdown-item" href="#">August - November</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="row">
            <!-- Card: Kehadiran Hari Ini -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card tale-bg">
                    <div class="card-people mt-auto">
                        <img src="assets/images/dashboard/people.svg" alt="people">
                        <div class="weather-info">
                            <div class="d-flex">
                                <div>
                                    <h2 class="mb-0 font-weight-normal">
                                        <i class="icon-user-check me-2"></i>{{ $todayAttendance }} hadir
                                    </h2>
                                </div>
                                <div class="ms-2">
                                    <h4 class="location font-weight-normal">Hari Ini</h4>
                                    <h6 class="font-weight-normal">{{ \Carbon\Carbon::now()->translatedFormat('l, d-m-Y') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Absensi -->
            <div class="col-md-6 grid-margin transparent">
                <div class="row">
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <div class="card-body">
                                <p class="mb-4">Total Kehadiran 30 Hari</p>
                                <p class="fs-30 mb-2">{{ $totalAttendance }}</p>
                                <p>{{ $attendancePercentage }}% dari total presensi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <p class="mb-4">Terlambat Hari Ini</p>
                                <p class="fs-30 mb-2">{{ $lateToday }}</p>
                                <p>{{ $latePercentage }}% dari total presensi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                        <div class="card card-light-blue">
                            <div class="card-body">
                                <p class="mb-4">Tidak Hadir Hari Ini</p>
                                <p class="fs-30 mb-2">{{ $absentToday }}</p>
                                <p>{{ $absentPercentage }}% dari total presensi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 stretch-card transparent">
                        <div class="card card-light-danger">
                            <div class="card-body">
                                <p class="mb-4">Cuti / Izin Hari Ini</p>
                                <p class="fs-30 mb-2">{{ $leaveToday }}</p>
                                <p>{{ $leavePercentage }}% dari total presensi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.app>
