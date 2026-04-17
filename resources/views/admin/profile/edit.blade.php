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
                                <canvas id="canvas" width="300" height="300" style="display:none;"></canvas>

                                <img id="preview" width="300" class="border rounded mb-2" style="display:none;">

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

        <script>
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const preview = document.getElementById('preview');

            // =========================
            // START CAMERA (FIX)
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
            // CAPTURE
            // =========================
            document.getElementById('captureBtn').onclick = () => {
                const ctx = canvas.getContext('2d');

                canvas.style.display = 'block';

                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                preview.src = canvas.toDataURL('image/png');
                preview.style.display = 'block';
            };

            // =========================
            // SAVE FACE (FIX BASE64)
            // =========================
            document.getElementById('saveBtn').onclick = async () => {

                if (!preview.src) {
                    Swal.fire('Error', 'Capture wajah dulu', 'error');
                    return;
                }

                // 🔥 convert base64 -> blob
                const res = await fetch(preview.src);
                const blob = await res.blob();

                const formData = new FormData();
                formData.append('user_id', document.getElementById('user_id').value);
                formData.append('face_descriptor', btoa(preview.src)); // sementara
                formData.append('face_image', blob, 'face.png'); // 🔥 FILE

                fetch('/face-register', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.fire(
                            data.status ? 'Success' : 'Error',
                            data.message,
                            data.status ? 'success' : 'error'
                        );
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Server error', 'error');
                    });
            };
        </script>
    @endpush

</x-admin.app>
