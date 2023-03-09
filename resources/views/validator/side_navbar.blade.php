<li class="nav-item">
    <a href="{{ route('validatordashboard') }}" class="nav-link {{ request()->is('validator/dashboard') ? 'active' : ''}}">
        <i class="nav-icon fa fa-tv"></i>
        <p>
            Dashboard
        </p>
    </a>
</li>
<li class="nav-header">Requisition</li>
<li class="nav-item ">
    <a href="{{ route('val_purchase_request') }}" class="nav-link {{ request()->is('validator/purchase_request') ? 'active' : ''}}">
        <i class="nav-icon fas fa-edit"></i>
        <p>
            My Purchase Request
        </p>
    </a>
</li>
<li class="nav-item ">
    <a href="{{ route('val_purchase_order') }}" class="nav-link {{ request()->is('processor/purchase_order') ? 'active' : ''}}">
        <i class="nav-icon fas fa-file"></i>
        <p>
            My Purchase Order
        </p>
    </a>
</li>
<li class="nav-item ">
    <a href="{{ route('val_to_received') }}" class="nav-link {{ request()->is('processor/val_to_received') ? 'active' : ''}}">
        <i class="nav-icon fas fa-calendar-alt"></i>
        <p>
            To Receive
        </p>
    </a>
</li>
<li class="nav-header">Procure Assignment</li>
<li class="nav-item active">
    <a href="{{ route('pr_check_fund') }}" class="nav-link {{ request()->is('validator/pr_check_fund') ? 'active' : ''}}">
        <i class="nav-icon fas fa-check"></i>
        <p>
            PR Check for Fund
        </p>
    </a>
</li>
<li class="nav-item active">
    <a href="{{ route('po_for_approval_val') }}" class="nav-link {{ request()->is('validator/po_for_approval') ? 'active' : ''}}">
        <i class="nav-icon fas fa-check"></i>
        <p>
            PO for Approval
        </p>
    </a>
</li>