<x-admin.app>
  <div class="content-wrapper">

    <div class="row">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Scan Absensi</h4>
            <p class="card-description">Silakan scan wajah untuk melakukan absensi</p>

            <div class="row">
              <!-- Kamera -->
              <div class="col-md-6 text-center mb-4 mb-md-0">
                <h5>Kamera</h5>
                <video id="video" width="100%" height="350" autoplay playsinline class="border rounded"></video>
              </div>

              <!-- Informasi Lokasi -->
              <div class="col-md-6">
                <h5>Informasi Lokasi</h5>

                <div class="mb-3">
                  <label>Latitude</label>
                  <input type="text" id="latitude" class="form-control" readonly>
                </div>

                <div class="mb-3">
                  <label>Longitude</label>
                  <input type="text" id="longitude" class="form-control" readonly>
                </div>

                <div class="mb-3">
                  <label>Akurasi GPS (meter)</label>
                  <input type="text" id="accuracy" class="form-control" readonly>
                </div>

                <!-- Hidden data untuk fetch -->
                <input type="hidden" id="user_id" value="{{ auth()->user()->id }}">
                <input type="hidden" id="office_id" value="{{ $office->id }}">
                <input type="hidden" id="device_name" value="{{ request()->header('User-Agent') }}">

                <div class="d-flex gap-3 mt-4">
                  <button type="button" class="btn btn-success" id="checkinBtn">
                    <i class="mdi mdi-login-variant me-1"></i> Check In
                  </button>
                  <button type="button" class="btn btn-danger" id="checkoutBtn">
                    <i class="mdi mdi-logout-variant me-1"></i> Check Out
                  </button>
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
    let latitude = null;
    let longitude = null;
    let accuracy = null;

    // ===== Kamera =====
    navigator.mediaDevices.getUserMedia({ video: true })
      .then(stream => {
        document.getElementById('video').srcObject = stream;
      })
      .catch(error => {
        Swal.fire({ icon:'error', title:'Kamera tidak bisa diakses', text: error.message });
      });

    // ===== GPS =====
    function updateLocation(pos) {
      latitude = pos.coords.latitude;
      longitude = pos.coords.longitude;
      accuracy = pos.coords.accuracy; // meter
      document.getElementById('latitude').value = latitude.toFixed(6);
      document.getElementById('longitude').value = longitude.toFixed(6);
      document.getElementById('accuracy').value = accuracy.toFixed(2);
    }

    function gpsError(err) {
      Swal.fire({ icon:'error', title:'Gagal mendapatkan lokasi', text: err.message });
    }

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(updateLocation, gpsError, { enableHighAccuracy: true });
    } else {
      Swal.fire({ icon:'error', title:'Geolocation tidak didukung browser Anda' });
    }

    // ===== Fungsi Haversine =====
    function haversineDistance(lat1, lon1, lat2, lon2) {
      const R = 6371000; // meter
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLon = (lon2 - lon1) * Math.PI / 180;
      const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      return R * c;
    }

    // ===== Fungsi Check In / Check Out =====
    function performAttendance(url) {
      if(!latitude || !longitude){
        Swal.fire({ icon:'error', title:'Lokasi belum tersedia' });
        return;
      }

      const payload = {
        user_id: document.getElementById('user_id').value,
        office_id: document.getElementById('office_id')?.value || null,
        device_name: document.getElementById('device_name').value,
        latitude: latitude,
        longitude: longitude,
        accuracy: accuracy // optional, bisa dipakai di backend
      };

      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(data => {
        let extra = '';
        if(data.distance) {
          extra = '\nJarak ke kantor: ' + data.distance + ' meter';
        }
        Swal.fire({
          icon: data.status ? 'success' : 'error',
          title: data.status ? 'Sukses' : 'Gagal',
          text: data.message + extra
        });
      })
      .catch(err => {
        Swal.fire({ icon:'error', title:'Terjadi kesalahan', text: err.message });
      });
    }

    // ===== Event Listener =====
    document.getElementById('checkinBtn').addEventListener('click', () => performAttendance('/check-in'));
    document.getElementById('checkoutBtn').addEventListener('click', () => performAttendance('/check-out'));

  </script>
  @endpush
</x-admin.app>
