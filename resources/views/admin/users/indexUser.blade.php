<x-admin.app>
  <div class="content-wrapper">
    <div class="row">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">

            <h4 class="card-title">Users Table</h4>
            <p class="card-description">Daftar semua user aplikasi</p>

            <!-- Alert Sukses / Error -->
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

            <!-- Tombol Tambah User -->
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createUserModal">
              Tambah User
            </button>

            <!-- Modal Create User -->
            <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <form action="{{ route('users.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                      <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" class="form-control" name="name" required>
                      </div>
                      <div class="mb-3">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" required>
                      </div>
                      <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                      </div>
                      <div class="mb-3">
                        <label>Address</label>
                        <textarea class="form-control" name="address" rows="3"></textarea>
                      </div>
                      <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-control">
                          <option value="super_admin">Super Admin</option>
                          <option value="admin">Admin</option>
                          <option value="user">User</option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                      </div>
                      <div class="mb-3">
                        <label>Konfirmasi Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Table Users -->
            <div class="table-responsive">
              <table class="table table-hover" id="users-table">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Role</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($users as $index => $user)
                    <tr>
                      <td>{{ $index + 1 }}</td>
                      <td>{{ $user->name }}</td>
                      <td>{{ $user->username }}</td>
                      <td>{{ $user->email }}</td>
                      <td>{{ $user->address }}</td>
                      <td>
                        <span class="badge {{ $user->role === 'admin' ? 'badge-primary' : 'badge-secondary' }}">
                          {{ ucfirst($user->role) }}
                        </span>
                      </td>
                      <td>
                        <div class="d-flex gap-2">
                          <!-- Tombol Edit -->
                          <button class="btn btn-warning btn-sm rounded-circle editUserBtn"
                                  style="width:35px;height:35px;padding:0"
                                  data-id="{{ $user->id }}"
                                  data-name="{{ $user->name }}"
                                  data-username="{{ $user->username }}"
                                  data-email="{{ $user->email }}"
                                  data-address="{{ $user->address }}"
                                  data-role="{{ $user->role }}"
                                  data-bs-toggle="modal" data-bs-target="#editUserModal">
                            <i class="fa fa-edit"></i>
                          </button>

                          <!-- Tombol Delete -->
                          <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="deleteUserForm">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm rounded-circle"
                                    style="width:35px;height:35px;padding:0">
                              <i class="fa fa-trash"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center">Tidak ada data user</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <!-- Modal Edit User -->
            <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                      <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                      </div>
                      <div class="mb-3">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" id="edit_username" required>
                      </div>
                      <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" id="edit_email" required>
                     </div>
                      <div class="mb-3">
                        <label>Address</label>
                        <textarea class="form-control" name="address" id="edit_address" rows="3"></textarea>
                      </div>
                      <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-control" id="edit_role">
                          <option value="admin">Admin</option>
                          <option value="operator">Operator</option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label>Password (kosongkan jika tidak diubah)</label>
                        <input type="password" class="form-control" name="password" id="edit_password">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                  </form>
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
      $(document).ready(function() {
        $('#users-table').DataTable();

        // Konfirmasi delete
        $('#users-table').on('submit', '.deleteUserForm', function(e){
          e.preventDefault();
          let form = this;
          Swal.fire({
            title: 'Yakin hapus user ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
          }).then((result) => {
            if(result.isConfirmed) form.submit();
          });
        });

        // Isi modal edit
        $('.editUserBtn').on('click', function(){
          let id = $(this).data('id');
          $('#edit_name').val($(this).data('name'));
          $('#edit_username').val($(this).data('username'));
          $('#edit_email').val($(this).data('email'));
          $('#edit_address').val($(this).data('address'));
          $('#edit_role').val($(this).data('role'));
          $('#editUserForm').attr('action', '/users/' + id);
        });
      });
    </script>
  @endpush
</x-admin.app>
