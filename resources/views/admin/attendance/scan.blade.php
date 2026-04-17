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
                                <video id="video" width="100%" height="350" autoplay playsinline
                                    class="border rounded"></video>
                                <canvas id="canvas" width="640" height="480" style="display:none;"></canvas>
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
        <script defer src="https://cdn.jsdelivr.net/npm/face-api.js"></script>

        <script>
            let latitude = null;
            let longitude = null;
            let accuracy = null;
            let faceReady = false;

            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');

            // =======================
            // LOAD MODEL FACE API
            // =======================
            async function loadFaceAPI() {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models')
                ]);
                faceReady = true;
                console.log("✅ Face API Ready");
            }

            loadFaceAPI();

            // =======================
            // START CAMERA
            // =======================
            async function startCamera() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: true
                    });
                    video.srcObject = stream;
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kamera tidak bisa diakses',
                        text: error.message
                    });
                }
            }

            startCamera();

            // =======================
            // GPS
            // =======================
            function updateLocation(pos) {
                latitude = pos.coords.latitude;
                longitude = pos.coords.longitude;
                accuracy = pos.coords.accuracy;

                document.getElementById('latitude').value = latitude.toFixed(6);
                document.getElementById('longitude').value = longitude.toFixed(6);
                document.getElementById('accuracy').value = accuracy.toFixed(2);
            }

            function gpsError(err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal mendapatkan lokasi',
                    text: err.message
                });
            }

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(updateLocation, gpsError, {
                    enableHighAccuracy: true
                });
            }

            // =======================
            // CAPTURE IMAGE
            // =======================
            function captureImage() {
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                return canvas.toDataURL('image/png');
            }

            // =======================
            // VALIDASI WAJAH
            // =======================
            async function validateFace() {

                if (!faceReady) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Model wajah belum siap'
                    });
                    return false;
                }

                Swal.fire({
                    title: 'Memverifikasi wajah...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const detection = await faceapi
                        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    if (!detection) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Wajah tidak terdeteksi!'
                        });
                        return false;
                    }

                    const currentDescriptor = detection.descriptor;

                    // 🔥 ambil data wajah user
                    const res = await fetch('/get-face-data/{{ auth()->user()->id }}');
                    const data = await res.json();

                    if (!data || !data.face_descriptor) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Wajah belum terdaftar!'
                        });
                        return false;
                    }

                    const savedDescriptor = new Float32Array(JSON.parse(data.face_descriptor));

                    // 🔥 hitung jarak
                    const distance = faceapi.euclideanDistance(currentDescriptor, savedDescriptor);

                    console.log("Distance:", distance);

                    Swal.close();

                    // 🔥 threshold
                    if (distance > 0.5) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Wajah tidak cocok!',
                            text: 'Coba ulangi dengan posisi yang jelas'
                        });
                        return false;
                    }

                    return true;

                } catch (err) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal verifikasi wajah',
                        text: err.message
                    });
                    return false;
                }
            }

            // =======================
            // ATTENDANCE
            // =======================
            async function performAttendance(url) {

                if (!latitude || !longitude) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lokasi belum tersedia'
                    });
                    return;
                }

                // 🔥 VALIDASI WAJAH
                const isValid = await validateFace();
                if (!isValid) return;

                const image = captureImage();

                const payload = {
                    user_id: document.getElementById('user_id').value,
                    office_id: document.getElementById('office_id').value,
                    device_name: document.getElementById('device_name').value,
                    latitude: latitude,
                    longitude: longitude,
                    accuracy: accuracy,
                    image: image
                };

                Swal.fire({
                    title: 'Mengirim absensi...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json();

                    Swal.close();

                    let extra = '';
                    if (data.distance) {
                        extra = '\nJarak ke kantor: ' + data.distance + ' meter';
                    }

                    Swal.fire({
                        icon: data.status ? 'success' : 'error',
                        title: data.status ? 'Sukses' : 'Gagal',
                        text: data.message + extra
                    });

                } catch (err) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan',
                        text: err.message
                    });
                }
            }

            // =======================
            // EVENT
            // =======================
            document.getElementById('checkinBtn')
                .addEventListener('click', () => performAttendance('/check-in'));

            document.getElementById('checkoutBtn')
                .addEventListener('click', () => performAttendance('/check-out'));
        </script>
    @endpush
</x-admin.app>
