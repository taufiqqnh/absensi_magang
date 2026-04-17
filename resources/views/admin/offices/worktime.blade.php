<x-admin.app>
  <div class="content-wrapper">
    <div class="row justify-content-center">
      <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">

            <h4 class="card-title">Office Work Time Setting</h4>
            <p class="card-description">Atur jam kerja office</p>

            {{-- Alert --}}
            @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif
            @if(session('error'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            <form action="{{ route('office.worktime.update') }}" method="POST">
              @csrf
              @method('PUT')

              <div class="mb-3">
                <label>Check In Time</label>
                <input type="time" class="form-control" name="check_in_time"
                       value="{{ old('check_in_time', $workTime->check_in_time ?? '09:00') }}" required>
              </div>

              <div class="mb-3">
                <label>Check Out Time</label>
                <input type="time" class="form-control" name="check_out_time"
                       value="{{ old('check_out_time', $workTime->check_out_time ?? '17:00') }}" required>
              </div>

              <div class="mb-3">
                <label>Toleransi Berangkat Terlambat (minutes)</label>
                <input type="number" class="form-control" name="late_tolerance"
                       value="{{ old('late_tolerance', $workTime->late_tolerance ?? 0) }}" min="0" required>
              </div>

              <div class="mb-3">
                <label>Toleransi Pulang Lebih Awal (minutes)</label>
                <input type="number" class="form-control" name="early_leave_tolerance"
                       value="{{ old('early_leave_tolerance', $workTime->early_leave_tolerance ?? 0) }}" min="0" required>
              </div>

              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Update</button>
              </div>

            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</x-admin.app>
