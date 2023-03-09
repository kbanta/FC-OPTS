<li class="nav-item">
    <a href="{{ route('processordashboard') }}" class="nav-link {{ request()->is('processor/dashboard') ? 'active' : ''}}">
        <i class="nav-icon fa fa-tv"></i>
        <p>
            Dashboard
        </p>
    </a>
</li>
<li class="nav-header">Manage Supplier & Items</li>
<li class="nav-item ">
    <a href="{{ route('pro_supplier_items') }}" class="nav-link {{ request()->is('processor/supplier_items') ? 'active' : ''}}">
        <i class="nav-icon far fa-plus-square"></i>
        <p>
            Supplier & Items
        </p>
    </a>
</li>
<li class="nav-header">Requisition</li>
<li class="nav-item ">
    <a href="{{ route('pro_purchase_request') }}" class="nav-link {{ request()->is('processor/purchase_request') ? 'active' : ''}}">
        <i class="nav-icon fas fa-edit"></i>
        <p>
            My Purchase Request
        </p>
    </a>
</li>
<li class="nav-item ">
    <a href="{{ route('pro_purchase_order') }}" class="nav-link {{ request()->is('processor/purchase_order') ? 'active' : ''}}">
        <i class="nav-icon far fa-file"></i>
        <p>
            My Purchase Order
        </p>
    </a>
</li>
<li class="nav-item ">
    <a href="{{ route('pro_to_received') }}" class="nav-link {{ request()->is('processor/pro_to_received') ? 'active' : ''}}">
        <i class="nav-icon fas fa-calendar-alt"></i>
        <p>
            To Receive
        </p>
    </a>
</li>
<li class="nav-header">Procure Assignment</li>
<li class="nav-item active">
    <a href="{{ route('pr_for_canvass') }}" class="nav-link {{ request()->is('processor/pr_for_canvass') ? 'active' : ''}}">
        <i class="nav-icon fas fa-search"></i>
        <p>
            PR for Canvass
        </p>
    </a>
</li>
<li class="nav-item active">
    <a href="{{ route('approved_pr') }}" class="nav-link {{ request()->is('processor/approved_pr') ? 'active' : ''}}">
        <i class="nav-icon fas fa-envelope"></i>
        <p>
            Approved PR
        </p>
    </a>
</li>
<li class="nav-item active">
    <a href="{{ route('pr_to_po') }}" class="nav-link {{ request()->is('processor/pr_to_po') ? 'active' : ''}}">
        <i class="nav-icon fas fa-copy "></i>
        <p>
            PR to PO
        </p>
    </a>
</li>
<li class="nav-item active">
    <a href="{{ route('approved_po') }}" class="nav-link {{ request()->is('processor/approved_po') ? 'active' : ''}}">
        <i class="nav-icon fas fa-file "></i>
        <p>
            Prepared PO
        </p>
    </a>
</li>
<li class="nav-item active">
    <a href="{{ route('order_po') }}" class="nav-link {{ request()->is('processor/order_po') ? 'active' : ''}}">
        <i class="nav-icon fas fa-shopping-cart "></i>
        <p>
            Order
        </p>
    </a>
</li>
<li class="nav-item active">
    <a href="{{ route('deliveries') }}" class="nav-link {{ request()->is('processor/deliveries') ? 'active' : ''}}">
        <i class="nav-icon fas fa-truck "></i>
        <p>
            Deliveries
        </p>
    </a>
</li>
<li class="nav-item active">
    <a href="{{ route('reported_items') }}" class="nav-link {{ request()->is('processor/reported_items') ? 'active' : ''}}">
        <i class="nav-icon fas fa-pencil-alt "></i>
        <p>
            Reported Items
        </p>
    </a>
</li>