<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Update Password</h4>
                <p class="card-description">Ensure your account is using a long, random password to stay secure.</p>

                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <!-- Current Password -->
                    <div class="form-group mb-3">
                        <label for="update_password_current_password">Current Password</label>
                        <div class="input-group">
                            <input id="update_password_current_password"
                                   name="current_password"
                                   type="password"
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="current-password">
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="mdi mdi-eye"></i>
                            </button>
                        </div>
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="form-group mb-3">
                        <label for="update_password_password">New Password</label>
                        <div class="input-group">
                            <input id="update_password_password"
                                   name="password"
                                   type="password"
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="new-password">
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="mdi mdi-eye"></i>
                            </button>
                        </div>
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group mb-3">
                        <label for="update_password_password_confirmation">Confirm Password</label>
                        <div class="input-group">
                            <input id="update_password_password_confirmation"
                                   name="password_confirmation"
                                   type="password"
                                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                                   autocomplete="new-password">
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="mdi mdi-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation', 'updatePassword')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="form-group d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary">Save</button>

                        @if (session('status') === 'password-updated')
                            <span class="text-success small ms-3">Saved.</span>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script toggle password -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".toggle-password").forEach(btn => {
            btn.addEventListener("click", function () {
                let input = this.previousElementSibling;
                if (input.type === "password") {
                    input.type = "text";
                    this.innerHTML = '<i class="mdi mdi-eye-off"></i>';
                } else {
                    input.type = "password";
                    this.innerHTML = '<i class="mdi mdi-eye"></i>';
                }
            });
        });
    });
</script>
