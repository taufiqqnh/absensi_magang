<x-admin.app>
  <div class="content-wrapper">
    <div class="row justify-content-center">
      <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">

            <h4 class="card-title">Office Setting</h4>
            <p class="card-description">Edit informasi office dan lokasi</p>

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

            <form action="{{ route('office.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Office Name</label>
                <input type="text" class="form-control" name="office_name" value="{{ old('office_name', $office->office_name) }}" required>
            </div>

            <div class="mb-3">
                <label>Address</label>
                <textarea class="form-control" name="address" rows="3">{{ old('address', $office->address) }}</textarea>
            </div>

            <hr>

            <h5>Office Location</h5>

            <div class="mb-3">
                <label>Latitude</label>
                <input type="number" step="any" class="form-control" id="latitude" name="latitude"
                    value="{{ old('latitude', $office->location->latitude ?? 0) }}" required>
            </div>

            <div class="mb-3">
                <label>Longitude</label>
                <input type="number" step="any" class="form-control" id="longitude" name="longitude"
                    value="{{ old('longitude', $office->location->longitude ?? 0) }}" required>
            </div>

            <div class="mb-3">
                <label>Radius (meter)</label>
                <input type="number" class="form-control" id="radius" name="radius"
                    value="{{ old('radius', $office->location->radius ?? 50) }}" min="0" required>
            </div>

            <button type="button" class="btn btn-info mb-3" id="scanLocationBtn">Scan Lokasi Saat Ini</button>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
<script>
document.getElementById('scanLocationBtn').addEventListener('click', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
            document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
            document.getElementById('radius').value = Math.round(position.coords.accuracy);

            // SweetAlert sukses
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Lokasi berhasil di-scan. Radius otomatis diisi dari akurasi GPS.',
                timer: 2500,
                timerProgressBar: true,
                showConfirmButton: false
            });
        }, function(error) {
            // SweetAlert error
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal mendapatkan lokasi: ' + error.message
            });
        }, {
            enableHighAccuracy: true
        });
    } else {
        // SweetAlert info
        Swal.fire({
            icon: 'info',
            title: 'Tidak didukung',
            text: 'Geolocation tidak didukung oleh browser Anda.'
        });
    }
});
</script>
@endpush
</x-admin.app>
