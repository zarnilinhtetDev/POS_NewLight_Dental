@include('layouts.header')

<style>
    .small-box {
        box-shadow: none !important;
    }
</style>


<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav col-md-6">
                <li class="nav-item">
                    <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>

                <li class="nav-item ">
                    <a class="nav-link text-white" href="#">Date -
                        <?= $currentDate = date('d-m-y') ?></a>
                </li>


            </ul>

            <!-- Right navbar links -->
            <ul class="ml-auto navbar-nav">

                <div class="btn-group">
                    <button type="button" class="btn dropdown-toggle text-white" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        {{ auth()->user()->name }}
                    </button>
                    <div class="dropdown-menu ">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-1 btn changelogout " style="width: 157px">
                                <i class="fa-solid fa-right-from-bracket "></i> Logout</button>

                        </form>


                    </div>
                </div>
            </ul>
        </nav>
        @include('layouts.sidebar')
        <div class="content-wrapper ">
            <!-- Main content -->
            <section class="content ">
                <section class="content-header">
                    <div class="container-fluid ">
                        <div class="mb-2 row">
                            <div class="col-sm-6">
                                <h1>Dashboard</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Dashboard
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
            </section>
            @php
                $userPermissions = [];
                if (auth()->user()->permission) {
                    $decodedPermissions = json_decode(auth()->user()->permission, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $userPermissions = $decodedPermissions;
                    }
                }
            @endphp

            @if (in_array('Dashboard', $userPermissions) || auth()->user()->is_admin == '1')
                <section class="content">
                    <div class="container-fluid">
                        <!-- Small boxes (Stat box) -->
                        <div class="row container-fluid">
                            <div class="dropdown show col-12 mb-3 mt-5">
                                {{-- <a class="btn btn-secondary dropdown-toggle" href="#" role="button"
                                    id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    @php
                                        $selectedWarehouse = request()->get('warehouse')
                                            ? \App\Models\Warehouse::find(request()->get('warehouse'))
                                            : null;
                                    @endphp
                                    {{ $selectedWarehouse ? $selectedWarehouse->name : 'All Location' }}
                                </a> --}}

                                {{-- <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item {{ request()->get('warehouse') == null ? 'active' : '' }}"
                                        href="{{ url()->current() }}">All Locations</a>
                                    @foreach ($warehouses as $warehouse)
                                        <a class="dropdown-item {{ request()->get('warehouse') == $warehouse->id ? 'active' : '' }}"
                                            href="{{ url()->current() . '?warehouse=' . $warehouse->id }}">{{ $warehouse->name }}</a>
                                    @endforeach
                                </div> --}}
                            </div>


                            <div class="container-fluid">
                                <div class="row">
                                    {{-- Invoice --}}
                                    <div class="col-md-4 mb-3">
                                        <div class="small-box bg-info " style="border-radius: 1rem;">
                                            <div class="inner">
                                                <h3>{{ $invoiceCount }}</h3>
                                                <p>Invoice</p>
                                                <strong>Total Price - {{ $invoices->sum('total') }}</strong>
                                            </div>
                                            <div class="icon">
                                                <i class="fa-solid fa-file-invoice-dollar"></i>
                                            </div>
                                            <a href="{{ url('invoice') }}" class="small-box-footer">
                                                More info <i class="fas fa-arrow-circle-right"></i>
                                            </a>
                                        </div>
                                    </div>

                                    {{-- POS --}}
                                    <div class="col-md-4 mb-3">
                                        <div class="small-box bg-danger" style="border-radius: 1rem;">
                                            <div class="inner">
                                                <h3>{{ $posCount }}</h3>
                                                <p>POS</p>
                                                <strong>Total Price - {{ $point_of_sales->sum('total') }}</strong>
                                            </div>
                                            <div class="icon">
                                                <i class="fa-solid fa-receipt"></i>
                                            </div>
                                            <a href="{{ url('pos') }}" class="small-box-footer">
                                                More info <i class="fas fa-arrow-circle-right"></i>
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Quotation --}}
                                    <div class="col-md-4 mb-3">
                                        <div class="small-box bg-warning" style="border-radius: 1rem;">
                                            <div class="inner">
                                                <h3>{{ $quotationCount }}</h3>
                                                <p>Quotation</p>
                                                <strong>Total Price - {{ $quotation->sum('total') }}</strong>
                                            </div>
                                            <div class="icon">
                                                <i class="fa-solid fa-file-lines"></i>
                                            </div>
                                            <a href="{{ url('quotation') }}" class="small-box-footer">
                                                More info <i class="fas fa-arrow-circle-right"></i>
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Purchase Order 1 --}}
                                    <div class="col-md-4 mb-3">
                                        <div class="small-box bg-primary" style="border-radius: 1rem;">
                                            <div class="inner">
                                                <h3>{{ $purchaseOrderCount }}</h3>
                                                <p>Purchase Order</p>
                                                <strong>Total Price - {{ $purchase_orders->sum('total') }}</strong>
                                            </div>
                                            <div class="icon">
                                                <i class="fa-solid fa-store"></i>
                                            </div>
                                            <a href="{{ url('purchase_order_manage') }}" class="small-box-footer">
                                                More info <i class="fas fa-arrow-circle-right"></i>
                                            </a>
                                        </div>
                                    </div>


                                    <div class="col-md-4 mb-3">
                                        <div class="small-box " style="background-color: #e88dbd; border-radius: 1rem;">
                                            <div class="inner">
                                                <h3>{{ $customerCount }}</h3>
                                                <p style="margin-top: 25px;margin-bottom: 25px;margin-left: 0px;">
                                                    Patient</p>
                                                <strong></strong>
                                            </div>
                                            <div class="icon">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            <a href="{{ url('patient') }}" class="small-box-footer">
                                                More info <i class="fas fa-arrow-circle-right"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="small-box " style="background-color: #46ec9c; border-radius: 1rem;">
                                            <div class="inner">
                                                <h3>{{ $monthlyCustomerCount }}
                                                </h3>

                                                <p style="margin-top: 25px;margin-bottom: 25px;margin-left: 0px;">
                                                    Monthly Patient</p>
                                                <strong></strong>
                                            </div>
                                            <div class="icon">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            <a href="{{ url('doctors') }}" class="small-box-footer">
                                                More info <i class="fas fa-arrow-circle-right"></i>
                                            </a>
                                        </div>
                                    </div>


                                </div>
                            </div>





                        </div>


                    </div>
                </section>
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="mt-3 bg-white card-header col-12 col-md-6 col-lg-6">

                                <h3 class="mx-3 my-2 mt-3 card-title">
                                    <i class="mr-1 fa-regular fa-file-lines font-weight-bold"></i>
                                    Invoices
                                </h3>

                                <canvas id="invoiceChart" width="600" height="300"></canvas>

                            </div>

                            <div class="mt-3 bg-white  card-header col-12 col-md-6 col-lg-6">

                                <h3 class="mx-3 my-2 mt-3 card-title">
                                    <i class="mr-1 fa-regular fa-file-lines font-weight-bold"></i>
                                    POS
                                </h3>


                                <canvas id="posChart" width="600" height="300"></canvas>

                            </div>

                            <div class="mt-3 bg-white  card-header col-6 col-md-6 col-lg-6">

                                <h3 class="mx-3 my-2 mt-3 card-title">
                                    <i class="mr-1 fa-regular fa-file-lines font-weight-bold"></i>
                                    Purchase Order
                                </h3>


                                <canvas id="poChart" width="600" height="300"></canvas>

                            </div>



                        </div>
                    </div>
                </section>
            @endif



</body>
<script src="{{ asset('backend/js/chart.js') }}"></script>


<script>
    var ctx = document.getElementById('invoiceChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($chart)) !!},
            datasets: [{
                label: 'Invoices Registrations by Month',
                data: {!! json_encode(array_values($chart)) !!},
                backgroundColor: [
                    '#3FB159',
                    '#C164D5',
                    '#F50003',
                    '#1BC9B5',
                    '#D8D8D8',
                    '#E95721',
                    '#F6BB00',
                ]
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: false, // Start from 0
                        min: 0, // Set the minimum value for y-axis
                        max: 1000 // Set the maximum value for y-axis
                    }
                }]
            }
        }
    });


    var ctx = document.getElementById('posChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($posChart)) !!},
            datasets: [{
                label: 'POS Registrations by Month',
                data: {!! json_encode(array_values($posChart)) !!},
                backgroundColor: [
                    '#3FB159',
                    '#C164D5',
                    '#F50003',
                    '#1BC9B5',
                    '#D8D8D8',
                    '#E95721',
                    '#F6BB00',
                ]
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: false, // Start from 0
                        min: 0, // Set the minimum value for y-axis
                        max: 1000 // Set the maximum value for y-axis
                    }
                }]
            }
        }
    });


    var ctx = document.getElementById('poChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($poChart)) !!},
            datasets: [{
                label: 'PurchaseOrder Registrations by Month',
                data: {!! json_encode(array_values($poChart)) !!},
                backgroundColor: [
                    '#3FB159',
                    '#C164D5',
                    '#F50003',
                    '#1BC9B5',
                    '#D8D8D8',
                    '#E95721',
                    '#F6BB00',
                ]
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: false, // Start from 0
                        min: 0, // Set the minimum value for y-axis
                        max: 1000 // Set the maximum value for y-axis
                    }
                }]
            }
        }
    });


    var ctx = document.getElementById('myChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($warehouseNames), // Warehouse names
            datasets: [{
                    label: 'Stock Out',
                    data: @json($transfer_out_data), // Transfer out data
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Stock In',
                    data: @json($transfer_in_data), // Transfer in data
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>




@include('layouts.footer')
