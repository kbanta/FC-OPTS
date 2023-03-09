<li class="nav-item">
    <a href="{{ route('admindashboard') }}" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : ''}}">
        <i class="nav-icon fa fa-tv"></i>
        <p>
            Dashboard
        </p>
    </a>
</li>
<li class="nav-header">Manage Accounts</li>
<li class="nav-item ">
    <a href="{{ route('account') }}" class="nav-link {{ request()->is('admin/manageAccount') ? 'active' : ''}}">
        <i class="nav-icon fas fa-users"></i>
        <p>
            Account
        </p>
    </a>
</li>
<li class="nav-header">Manage Facilities</li>
<li class="nav-item">
    <a href="{{ route('facility') }}" class="nav-link {{ request()->is('admin/facility') ? 'active' : ''}}">
        <i class="nav-icon fas fa-hotel"></i>
        <p>
            Building & Department
        </p>
    </a>
</li>
<li class="nav-header">Requisition</li>
<li class="nav-item ">
    <a href="{{ route('purchase_request') }}" class="nav-link {{ request()->is('admin/purchase_request') ? 'active' : ''}}">
        <i class="nav-icon fas fa-edit"></i>
        <p>
            My Purchase Request
        </p>
    </a>
</li>
<li class="nav-item ">
    <a href="{{ route('ad_purchase_order') }}" class="nav-link {{ request()->is('admin/purchase_order') ? 'active' : ''}}">
        <i class="nav-icon fas fa-file"></i>
        <p>
            My Purchase Order
        </p>
    </a>
</li>
<li class="nav-item ">
    <a href="{{ route('ad_to_received') }}" class="nav-link {{ request()->is('admin/ad_to_received') ? 'active' : ''}}">
        <i class="nav-icon fas fa-calendar-alt"></i>
        <p>
            To Received
        </p>
    </a>
</li>