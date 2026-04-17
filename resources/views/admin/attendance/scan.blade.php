<x-admin.app>
    <div class="content-wrapper">

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Scan Absensi</h4>
                        <p class="card-description">Silakan scan wajah untuk melakukan absensi</p>

                        <div class="row">

                            <!-- CAMERA -->
                            <div class="col-md-8 text-center mb-4">
                                <h5>Kamera</h5>

                                <video id="video" width="640" height="480" autoplay playsinline muted
                                    class="border rounded"></video>

                                <canvas id="canvas" width="640" height="480" style="display:none;"></canvas>
                            </div>

                            <!-- GPS -->
                            <div class="col-md-4">

                                <h5>Informasi Lokasi</h5>

                                <input type="text" id="latitude" class="form-control mb-2" readonly>
                                <input type="text" id="longitude" class="form-control mb-2" readonly>
                                <input type="text" id="accuracy" class="form-control mb-2" readonly>

                                <input type="hidden" id="user_id" value="{{ auth()->user()->id }}">
                                <input type="hidden" id="office_id" value="{{ $office->id }}">
                                <input type="hidden" id="device_name" value="{{ request()->header('User-Agent') }}">

                                <div class="d-flex gap-2 mt-3">
                                    <button class="btn btn-success" id="checkinBtn">Check In</button>
                                    <button class="btn btn-danger" id="checkoutBtn">Check Out</button>
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
        <script src="assets/js/face-api.min.js"></script>

        <script>
            let latitude = null;
            let longitude = null;
            let accuracy = null;
            let modelsLoaded = false;

            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const ctx = canvas.getContext('2d');


            // =====================
            // LOAD MODEL
            // =====================
            async function loadModels() {
                try {
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                        faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                        faceapi.nets.faceRecognitionNet.loadFromUri('/models')
                    ]);

                    modelsLoaded = true;
                    console.log("FACE API READY");

                } catch (err) {
                    Swal.fire('Error', 'Model gagal load: ' + err.message, 'error');
                }
            }

            loadModels();


            // =====================
            // CAMERA
            // =====================
            async function startCamera() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: true,
                        audio: false
                    });

                    video.srcObject = stream;

                    video.onloadedmetadata = () => {
                        video.play();
                    };

                } catch (err) {
                    Swal.fire('Camera Error', err.message, 'error');
                }
            }

            startCamera();


            // =====================
            // GPS
            // =====================
            navigator.geolocation.getCurrentPosition((pos) => {
                latitude = pos.coords.latitude;
                longitude = pos.coords.longitude;
                accuracy = pos.coords.accuracy;

                document.getElementById('latitude').value = latitude.toFixed(6);
                document.getElementById('longitude').value = longitude.toFixed(6);
                document.getElementById('accuracy').value = accuracy.toFixed(2);
            });


            // =====================
            // FACE VALIDATION
            // =====================
            async function validateFace() {

                if (!modelsLoaded) {
                    Swal.fire('Tunggu', 'Model belum siap', 'warning');
                    return false;
                }

                Swal.fire({
                    title: 'Scanning wajah...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {

                    const detection = await faceapi
                        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                            inputSize: 512,
                            scoreThreshold: 0.3
                        }))
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    if (!detection) {
                        Swal.close();
                        Swal.fire('Error', 'Wajah tidak terdeteksi', 'error');
                        return false;
                    }

                    const current = detection.descriptor;

                    const res = await fetch('/get-face-data/{{ auth()->user()->id }}');
                    const data = await res.json();

                    if (!data.status || !data.face_descriptor) {
                        Swal.close();
                        Swal.fire('Error', 'Face belum terdaftar', 'error');
                        return false;
                    }

                    const saved = new Float32Array(JSON.parse(data.face_descriptor));

                    const distance = faceapi.euclideanDistance(current, saved);

                    console.log("Distance:", distance);

                    Swal.close();

                    if (distance > 0.6) {
                        Swal.fire('Gagal', 'Wajah tidak cocok', 'error');
                        return false;
                    }

                    return true;

                } catch (err) {
                    Swal.close();
                    Swal.fire('Error', err.message, 'error');
                    return false;
                }
            }


            // =====================
            // CAPTURE IMAGE
            // =====================
            function captureImage() {
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                return canvas.toDataURL('image/png');
            }


            // =====================
            // ABSENSI
            // =====================
            async function performAttendance(url) {

                if (!latitude || !longitude) {
                    Swal.fire('Error', 'GPS belum siap', 'error');
                    return;
                }

                const valid = await validateFace();
                if (!valid) return;

                const payload = {
                    user_id: document.getElementById('user_id').value,
                    office_id: document.getElementById('office_id').value,
                    device_name: document.getElementById('device_name').value,
                    latitude,
                    longitude,
                    accuracy,
                    image: captureImage()
                };

                Swal.fire({
                    title: 'Mengirim...',
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

                    Swal.fire(
                        data.status ? 'Berhasil' : 'Gagal',
                        data.message,
                        data.status ? 'success' : 'error'
                    );

                } catch (err) {
                    Swal.close();
                    Swal.fire('Error', err.message, 'error');
                }
            }


            // =====================
            // BUTTON
            // =====================
            document.getElementById('checkinBtn').onclick = () => performAttendance('/check-in');
            document.getElementById('checkoutBtn').onclick = () => performAttendance('/check-out');
        </script>
    @endpush
</x-admin.app>
