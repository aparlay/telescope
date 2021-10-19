<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <span class="brand-text font-weight-light"><strong>Admin</strong> Dashboard</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ auth()->user()->avatar }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->username }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('core.admin.dashboard') }}" class="nav-link {{ Route::currentRouteName() === 'core.admin.dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-header">Resources </li>
                <li class="nav-item">
                    <a href="{{ route('core.admin.user.index') }}" class="nav-link {{ Route::currentRouteName() === 'core.admin.user.index' ? 'active' : '' }}">
                        <i class="nav-icon far fa-user text-info"></i>
                        <p>Users</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('core.admin.media.index') }}" class="nav-link {{ Route::currentRouteName() === 'core.admin.media.index' ? 'active' : '' }}">
                        <i class="nav-icon far fa-file-video text-info"></i>
                        <p>Media</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('payment.admin.payment.index') }}" class="nav-link {{ Route::currentRouteName() === 'core.admin.media.index' ? 'active' : '' }}">
                        <i class="nav-icon far fa-money text-info"></i>
                        <p>Payment</p>
                    </a>
                    <ul class="nav nav-treeview" style="display: block;">
                        <li class="nav-item">
                            <a href="pages/layout/top-nav.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tip</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="pages/layout/boxed.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Subscription</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="pages/layout/fixed-sidebar.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Credit Cards</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="pages/layout/fixed-topnav.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Orders</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="pages/layout/fixed-footer.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Transactions</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('core.admin.media.index') }}" class="nav-link {{ Route::currentRouteName() === 'core.admin.media.index' ? 'active' : '' }}">
                        <i class="nav-icon far fa-file-video text-info"></i>
                        <p>Payout</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('core.admin.logout') }}" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt text-info"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
