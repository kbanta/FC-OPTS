<li class="nav-item">
    <a href="{{ route('approverdashboard') }}" class="nav-link {{ request()->is('approver/dashboard') ? 'active' : ''}}">
        <i class="nav-icon fa fa-tv"></i>
        <p>
            Dashboard
        </p>
    </a>
</li>
<li class="nav-header">Requisition</li>
<li class="nav-item ">
    <a href="{{ route('app_purchase_request') }}" class="nav-link {{ request()->is('approver/purchase_request') ? 'active' : ''}}">
        <i class="nav-icon fas fa-edit"></i>
        <p>
            My Purchase Request
        </p>
    </a>
</li>
<li class="nav-item ">
    <a href="{{ route('app_purchase_order') }}" class="nav-link {{ request()->is('approver/purchase_order') ? 'active' : ''}}">
        <i class="nav-icon fas fa-file"></i>
        <p>
            My Purchase Order
        </p>
    </a>
</li>
<li class="nav-item ">
    <a href="{{ route('app_to_received') }}" class="nav-link {{ request()->is('approver/app_to_received') ? 'active' : ''}}">
        <i class="nav-icon fas fa-calendar-alt"></i>
        <p>
            To Receive
        </p>
    </a>
</li>
<li class="nav-header">Procure Assignment</li>
@if(Auth::user()->position == 'ASSD Manager')
<li class="nav-item active">
    <a href="{{ route('new_pr') }}" class="nav-link {{ request()->is('approver/new_pr') ? 'active' : ''}}">
        <i class="nav-icon far fa-envelope"></i>
        <p>
            New Purchase Request
        </p>
    </a>
</li>
<li class="nav-item active">
    <a href="{{ route('pr_for_verification') }}" class="nav-link {{ request()->is('approver/pr_for_verification') ? 'active' : ''}}">
        <i class="nav-icon fas fa-search"></i>
        <p>
            PR for Verification
        </p>
    </a>
</li>
<li class="nav-item active">
    <a href="{{ route('po_for_approval') }}" class="nav-link {{ request()->is('approver/po_for_approval') ? 'active' : ''}}">
        <i class="nav-icon fas fa-check"></i>
        <p>
            PO for Approval
        </p>
    </a>
</li>
<li class="nav-item active">
    <a href="{{ route('to_transmit') }}" class="nav-link {{ request()->is('approver/to_transmit') ? 'active' : ''}}">
        <i class="nav-icon fas fa-paper-plane "></i>
        <p>
            To Transmit
        </p>
    </a>
</li>
@else
<li class="nav-item active">
    <a href="{{ route('pr_for_approval') }}" class="nav-link {{ request()->is('approver/pr_for_approval') ? 'active' : ''}}">
        <i class="nav-icon fas fa-check"></i>
        <p>
            PR for Approval
        </p>
    </a>
</li>
<li class="nav-item active">
    <a href="{{ route('po_for_approval') }}" class="nav-link {{ request()->is('approver/po_for_approval') ? 'active' : ''}}">
        <i class="nav-icon fas fa-check"></i>
        <p>
            PO for Approval
        </p>
    </a>
</li>
@endif