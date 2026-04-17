<div class="row mt-4">
    <div class="col-md-12">
        <div class="card border-danger">
            <div class="card-body">
                <h4 class="card-title text-danger">Delete Account</h4>
                <p class="card-description">
                    Once your account is deleted, all of its resources and data will be permanently deleted.
                    Before deleting your account, please download any data or information that you wish to retain.
                </p>

                <!-- Trigger Button -->
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade @if($errors->userDeletion->isNotEmpty()) show @endif"
     id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true"
     @if($errors->userDeletion->isNotEmpty()) style="display:block;" @endif>
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteAccountModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p>
                        Once your account is deleted, all of its resources and data will be permanently deleted.
                        Please enter your password to confirm you would like to permanently delete your account.
                    </p>

                    <!-- Password input -->
                    <div class="form-group">
                        <label for="delete_password">Password</label>
                        <input id="delete_password"
                               name="password"
                               type="password"
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                               placeholder="Password"
                               required>
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
