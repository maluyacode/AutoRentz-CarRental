<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admindashboard') }}" class="brand-link">
        <img src="{{ url('storage/images/Logo.png') }}" alt="AdminLTE Logo" class="brand-image img-square elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-bold"
            style="margin-left: 5px; font-family:'Times New Roman', Times, serif">AutoRentz</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ Auth::user()->customer->image_path }}" class="img-square elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
       with font-awesome or any other icon font library -->
                <li class="nav-item menu-open">
                    <a href="{{ route('admindashboard') }}" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <style>
                    .child-item {
                        margin-left: 10px;
                    }
                </style>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa fa-light fa-car" style="margin: 0 2px"></i>
                        <p>
                            Autoscape
                            <i class="fas fa-angle-left right"></i>
                            {{-- <span class="badge badge-info right">6</span> --}}
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item child-item">
                            <a href="{{ route('cars.page') }}" class="nav-link">
                                <i class="far fa-circle nav-icon "></i>
                                <p>Vehicles (MP1)</p>
                            </a>
                        </li>
                        <li class="nav-item child-item">
                            <a href="{{ route('model.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Models</p>
                            </a>
                        </li>
                        <li class="nav-item child-item">
                            <a href="{{ route('manufacturers.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manufacturers</p>
                            </a>
                        </li>
                        <li class="nav-item child-item">
                            <a href="{{ route('types.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Car Types</p>
                            </a>
                        </li>
                        <li class="nav-item child-item">
                            <a href="{{ route('fuel.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Fuel Types</p>
                            </a>
                        </li>
                        <li class="nav-item child-item">
                            <a href="{{ route('transmission.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Transmissions</p>
                            </a>
                        </li>
                        <li class="nav-item child-item">
                            <a href="{{ route('accessories.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Accessories (MP3)</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('bookings.index') }}" class="nav-link">
                        <i class="fa fa-thin fa-book" style="margin: 0 2px"></i>
                        <p>
                            Manage Bookings
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item child-item">
                            <a href="{{ route('bookings.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Bookings</p>
                            </a>
                        </li>
                        <li class="nav-item child-item">
                            <a href="{{ route('adminPendings') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pendings</p>
                            </a>
                        </li>
                        <li class="nav-item child-item">
                            <a href="{{ route('adminConfirms') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Confirmed</p>
                            </a>
                        </li>
                        <li class="nav-item child-item">
                            <a href="{{ route('adminFinish') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Finished</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('drivers.page') }}" class="nav-link">
                        <i class="fa fa-thin fa-id-card" style="margin: 0 2px"></i>
                        <p>
                            Drivers (MP2)
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('location.index') }}" class="nav-link">
                        <i class="fa fa-sharp fa-light fa-location-arrow" style="margin: 0 2px"></i>
                        <p>
                            Locations (MP4)
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="fa fa-solid fa-user" style="margin: 0 2px"></i>
                        <p>
                            User Management
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('report') }}" class="nav-link">
                        <i class="fa fa-sharp fa-regular fa-fax" style="margin: 0 2px"></i>
                        <p>
                            Report
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
