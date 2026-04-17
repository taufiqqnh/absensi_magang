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
                    Kelola informasi akun, password, penghapusan akun, dan pendaftaran wajah untuk absensi.
                </p>
            </div>
        </div>

        <div class="row">
            <!-- Update Profile -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Update Profile Information</h4>
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

        <!-- Face Registration -->
        <div class="row mt-4">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card border-primary">
                    <div class="card-body">
                        <h4 class="card-title text-primary">Register Face for Attendance</h4>
                        <hr>
                        <p>Gunakan kamera untuk mendaftarkan wajah Anda.</p>

                        <div class="row">
                            <div class="col-md-6 text-center">
                                <video id="video" width="100%" height="300" autoplay playsinline
                                    class="border rounded"></video>
                                <button type="button" class="btn btn-info mt-3" id="captureBtn">Capture Face</button>
                            </div>
                            <div class="col-md-6">
                                <canvas id="canvas" width="300" height="300" class="border rounded mb-2"
                                    style="display:none;"></canvas>
                                <img id="facePreview" width="300" style="display:none;"
                                    class="mb-2 rounded border" />
                                <button type="button" class="btn btn-success" id="saveFaceBtn">Save Face</button>
                            </div>
                        </div>

                        <!-- Hidden input untuk user -->
                        <input type="hidden" id="user_id" value="{{ auth()->user()->id }}">

                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Account -->
        <div class="row mt-4">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card border-danger">
                    <div class="card-body">
                        <h4 class="card-title text-danger">Delete Account</h4>
                        <hr>
                        @include('admin.profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- end content-wrapper -->

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const facePreview = document.getElementById('facePreview');

            // ===== Kamera =====
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => Swal.fire({
                    icon: 'error',
                    title: 'Kamera tidak bisa diakses'
                }));

            // ===== Capture Face =====
            document.getElementById('captureBtn').addEventListener('click', function() {
                const context = canvas.getContext('2d');
                canvas.style.display = 'block';
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Preview
                facePreview.src = canvas.toDataURL('image/png');
                facePreview.style.display = 'block';
            });

            // ===== Save Face =====
            document.getElementById('saveFaceBtn').addEventListener('click', async function() {
                if (!facePreview.src) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Belum ada wajah yang di-capture'
                    });
                    return;
                }

                // Dummy face_descriptor (bisa diganti library face recognition nanti)
                const face_descriptor = btoa(facePreview.src);

                // Convert Base64 ke Blob supaya Laravel bisa menerima sebagai file
                const response = await fetch(facePreview.src);
                const blob = await response.blob();
                const formData = new FormData();
                formData.append('user_id', document.getElementById('user_id').value);
                formData.append('face_descriptor', face_descriptor);
                formData.append('face_image', blob, 'face.png');

                fetch('/face-register', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.fire({
                            icon: data.status ? 'success' : 'error',
                            title: data.message
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan server'
                        });
                    });
            });
        </script>
    @endpush

</x-admin.app>
