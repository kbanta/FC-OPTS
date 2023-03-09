<li class="nav-item">
    <a href="{{ route('requestordashboard') }}" class="nav-link {{ request()->is('requestor/dashboard') ? 'active' : ''}}">
        <i class="nav-icon fa fa-tv"></i>
        <p>
            Dashboard
        </p>
    </a>
</li>
<li class="nav-header">Requisition</li>
<li class="nav-item ">
    <a href="{{ route('req_purchase_request') }}" class="nav-link {{ request()->is('requestor/purchase_request') ? 'active' : ''}}">
        <i class="nav-icon fas fa-edit"></i>
        <p>
            My Purchase Request
        </p>
    </a>
</li>
<li class="nav-item ">
    <a href="{{ route('req_purchase_order') }}" class="nav-link {{ request()->is('requestor/purchase_order') ? 'active' : ''}}">
        <i class="nav-icon fas fa-file"></i>
        <p>
            My Purchase Order
        </p>
    </a>
</li>
<li class="nav-item ">
    <a href="{{ route('to_received') }}" class="nav-link {{ request()->is('requestor/to_received') ? 'active' : ''}}">
        <i class="nav-icon fas fa-calendar-alt"></i>
        <p>
            To Receive
        </p>
    </a>
</li>