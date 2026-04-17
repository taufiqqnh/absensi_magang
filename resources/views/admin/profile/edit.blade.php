<x-admin.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="content-wrapper">

        <div class="row">
            <div class="col-md-12 grid-margin">
                <h2 class="font-weight-bold">Profile Settings</h2>
                <p class="mb-4">
                    Kelola informasi akun, password, dan pendaftaran wajah untuk absensi.
                </p>
            </div>
        </div>

        <div class="row">

            <!-- Update Profile -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Update Profile</h4>
                        <hr>
                        @include('admin.profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Update Password -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Update Password</h4>
                        <hr>
                        @include('admin.profile.partials.update-password-form')
                    </div>
                </div>
            </div>

        </div>

        <!-- FACE REGISTRATION -->
        <div class="row mt-4">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card border-primary">
                    <div class="card-body">

                        <h4 class="card-title text-primary">Register Face</h4>
                        <p>Scan wajah untuk absensi</p>

                        <div class="row">
                            <div class="col-md-6 text-center">

                                <video id="video" width="100%" height="300" autoplay muted playsinline
                                    class="border rounded"></video>

                                <button class="btn btn-info mt-3" id="captureBtn">
                                    Capture
                                </button>
                            </div>

                            <div class="col-md-6 text-center">

                                <canvas id="canvas" style="display:none;"></canvas>

                                <img id="preview" width="300" class="border rounded mb-2" style="display:none;" />

                                <button class="btn btn-success" id="saveBtn">
                                    Save Face
                                </button>

                            </div>
                        </div>

                        <input type="hidden" id="user_id" value="{{ auth()->user()->id }}">

                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="assets/js/face-api.min.js"></script>

        <script>
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const preview = document.getElementById('preview');

            const captureBtn = document.getElementById('captureBtn');
            const saveBtn = document.getElementById('saveBtn');

            let capturedBlob = null;
            let faceDescriptor = null;
            let modelsLoaded = false;


            // =========================
            // LOAD MODEL FACE API
            // =========================
            async function loadModels() {
                try {
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                        faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                        faceapi.nets.faceRecognitionNet.loadFromUri('/models')
                    ]);

                    modelsLoaded = true;
                    console.log("Face API Ready");

                } catch (err) {
                    Swal.fire('Error', 'Model gagal load: ' + err.message, 'error');
                }
            }

            loadModels();


            // =========================
            // START CAMERA
            // =========================
            async function startCamera() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: "user"
                        },
                        audio: false
                    });

                    video.srcObject = stream;
                    await video.play();

                } catch (err) {
                    console.error(err);
                    Swal.fire('Error', 'Kamera tidak bisa diakses', 'error');
                }
            }

            startCamera();


            // =========================
            // CAPTURE + FACE DETECTION
            // =========================
            captureBtn.addEventListener('click', async () => {

                const ctx = canvas.getContext('2d');

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                const base64 = canvas.toDataURL('image/png');

                preview.src = base64;
                preview.style.display = 'block';

                // convert blob
                capturedBlob = await fetch(base64).then(res => res.blob());


                // =========================
                // AMBIL FACE DESCRIPTOR
                // =========================
                if (!modelsLoaded) {
                    Swal.fire('Tunggu', 'Model belum siap', 'warning');
                    return;
                }

                const detection = await faceapi
                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                        inputSize: 512,
                        scoreThreshold: 0.3
                    }))
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (!detection) {
                    Swal.fire('Error', 'Wajah tidak terdeteksi', 'error');
                    return;
                }

                faceDescriptor = detection.descriptor;

                console.log("Face Descriptor:", faceDescriptor);
            });


            // =========================
            // SAVE FACE
            // =========================
            saveBtn.addEventListener('click', async () => {

                if (!capturedBlob) {
                    Swal.fire('Error', 'Capture wajah dulu', 'error');
                    return;
                }

                if (!faceDescriptor) {
                    Swal.fire('Error', 'Face tidak terdeteksi, ulang capture', 'error');
                    return;
                }

                saveBtn.disabled = true;
                saveBtn.innerText = 'Saving...';

                try {

                    const formData = new FormData();

                    formData.append('user_id', document.getElementById('user_id').value);

                    // IMAGE
                    formData.append('face_image', capturedBlob, 'face.png');

                    // FACE DESCRIPTOR (INI PENTING 🔥)
                    formData.append(
                        'face_descriptor',
                        JSON.stringify(Array.from(faceDescriptor))
                    );

                    const response = await fetch('/face-register', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    Swal.fire(
                        data.status ? 'Success' : 'Error',
                        data.message,
                        data.status ? 'success' : 'error'
                    );

                } catch (err) {
                    console.error(err);
                    Swal.fire('Error', err.message, 'error');
                }

                saveBtn.disabled = false;
                saveBtn.innerText = 'Save Face';
            });
        </script>
    @endpush

</x-admin.app>
