<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">

    {{-- Semua role bisa lihat Dashboard --}}
    <li class="nav-item">
      <a class="nav-link" href="/dashboard">
        <i class="fa fa-area-chart menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    @if(auth()->user()->role != 'magang')
      <li class="nav-item">
        <a class="nav-link" href="/office">
          <i class="fa fa-building menu-icon"></i>
          <span class="menu-title">Office</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/worktime">
          <i class="fa fa-clock-o menu-icon"></i>
          <span class="menu-title">Jam Kerja Office</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/users-admin">
          <i class="fa fa-users menu-icon"></i>
          <span class="menu-title">Users</span>
        </a>
      </li>
    @endif

    {{-- Role magang & role lain bisa lihat ini --}}
    <li class="nav-item">
      <a class="nav-link" href="/attendance">
        <i class="fa fa-calendar-o menu-icon"></i>
        <span class="menu-title">Data Absensi</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="/scan">
        <i class="fa fa-book menu-icon"></i>
        <span class="menu-title">Absensi</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="/profile">
       <i class="fa fa-user-circle menu-icon"></i>
        <span class="menu-title">Profile</span>
      </a>
    </li>

  </ul>
</nav>
