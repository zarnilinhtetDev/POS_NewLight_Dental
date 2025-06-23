<style>
    .main-sidebar {
        background: linear-gradient(to bottom, #0047AA, #2270c9);
    }
</style>
<aside class="main-sidebar sidebar-primary elevation-4">
    <!-- Brand Logo -->
    <span class="text-center brand-link ">
        <span class="text-white brand-text font-weight-bold">SSE POS</span>
    </span>


    <!-- Sidebar -->
    <div class="sidebar ">


        @php
            $userPermissions = [];
            if (auth()->user()->permission) {
                $decodedPermissions = json_decode(auth()->user()->permission, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $userPermissions = $decodedPermissions;
                }
            }
        @endphp


        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column " data-widget="treeview" role="menu" data-accordion="false">

                @if (in_array('Dashboard', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/dashboard') }}" class="nav-link">
                            <i class="text-white fa-solid fa-house nav-icon "></i>
                            <p class="pl-3 text-white">
                                Dashboard </p>
                        </a>
                    </li>
                @endif

                @if (in_array('Treatment', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="text-white nav-icon fas fa-table"></i>
                            <p class="pl-3 text-white">
                                Treatement (service)
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('items') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Treatement</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('items_register') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon "></i>
                                    <p class="text-white">Treatement Register</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (in_array('Patient', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/patient') }}" class="nav-link">
                            <i class="text-white fa-solid fa-user-plus nav-icon"></i>
                            <p class="pl-3 text-white">
                                Patient </p>
                        </a>

                    </li>
                @endif

                @if (in_array('POS', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/pos') }}" class="nav-link">
                            <i class="text-white fa-solid fa-cart-plus nav-icon"></i>
                            <p class="pl-3 text-white">
                                POS
                            </p><i class="text-white right fas fa-angle-left"></i>
                        </a>


                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('pos') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">POS Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('pos_register') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Issue POS</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endif

                @if (in_array('Invoice', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/invoice') }}" class="nav-link">
                            <i class="text-white nav-icon fas fa-copy"></i>
                            <p class="pl-3 text-white">
                                Invoice
                            </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('invoice') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Invoice Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('invoice_reg') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Issue Invoice</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endif

                @if (in_array('Quotation', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/quotation') }}" class="nav-link">

                            <i class="text-white fa-solid fa-file-invoice-dollar nav-icon"></i>
                            <p class="pl-3 text-white">
                                Quotation
                            </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('quotation') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Quotation Manage</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('quotation_register') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Issue Quotation</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (in_array('Purchase Order', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="text-white fa-solid fa-receipt nav-icon"></i>
                            <p class="pl-3 text-white">
                                Purchase Order
                            </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('purchase_order_manage') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white"> Purchase Order Manage</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('purchase_order_register') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Issue Purchase Order</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (in_array('Warehouse', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/warehouse') }}" class="nav-link">
                            <i class="text-white fa-solid fa-house nav-icon "></i>
                            <p class="pl-3 text-white">
                                Location
                            </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/warehouse') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Location Manage</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('transfer_item') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Transfer Item</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('show_transfer_history') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Transfer History</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (in_array('Expense', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('expense') }}" class="nav-link">
                            <i class="text-white fa-solid fa-money-check-dollar nav-icon"></i>
                            <p class="pl-3 text-white">
                                Expenses </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/expense') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Expense Register</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('expense_category') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Expense Category</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (in_array('Doctor', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/doctors') }}" class="nav-link">
                            <i class="text-white fa-solid fa-user-doctor nav-icon"></i>
                            <p class="pl-3 text-white">Doctors </p>
                        </a>

                    </li>
                @endif

                @if (in_array('Report', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="text-white fa-solid fa-receipt nav-icon"></i>
                            <p class="pl-3 text-white">
                                Report
                            </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('report') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Invoices</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('report_item') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Treatment Items</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('report_clinic_item') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Clinic Items</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('report_expense') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Expenses</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ url('doctor') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Doctors</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ url('profit') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Profit</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif


                @if (in_array('User', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a class="nav-link">
                            <i class="text-white fa-solid fa-users nav-icon"></i>
                            <p class="pl-3 text-white">
                                User </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/user') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">User</p>
                                </a>
                            </li>
                            @if (in_array('User Type', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('/user_type') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">User Type</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

            </ul>
        </nav>





    </div>
</aside>
