<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Profile Information</h4>
                <p class="card-description">Update your account's profile information, username and email address.</p>

                <!-- Form verifikasi email -->
                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                    @csrf
                </form>

                <!-- Form Update Profile -->
                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <!-- Name -->
                    <div class="form-group mb-3">
                        <label for="name">Name</label>
                        <input id="name"
                               name="name"
                               type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}"
                               required
                               autofocus
                               autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="form-group mb-3">
                        <label for="username">Username</label>
                        <input id="username"
                               name="username"
                               type="text"
                               class="form-control @error('username') is-invalid @enderror"
                               value="{{ old('username', $user->username) }}"
                               required
                               autocomplete="username">
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group mb-3">
                        <label for="email">Email address</label>
                        <input id="email"
                               name="email"
                               type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}"
                               required
                               autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2">
                                <p class="text-danger small">
                                    Your email address is unverified.
                                    <button form="send-verification" class="btn btn-link p-0 m-0 align-baseline">
                                        Click here to re-send the verification email.
                                    </button>
                                </p>

                                @if (session('status') === 'verification-link-sent')
                                    <p class="text-success small">
                                        A new verification link has been sent to your email address.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Submit -->
                    <div class="form-group d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary">Save</button>

                        @if (session('status') === 'profile-updated')
                            <span class="text-success small ms-3">Saved.</span>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
