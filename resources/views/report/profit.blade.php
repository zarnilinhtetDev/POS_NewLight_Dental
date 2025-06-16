@include('layouts.header')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav col-md-6">
                <li class="nav-item">
                    <a class="text-white nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>

                <li class="nav-item">
                    <a class="text-white nav-link" href="#">Date -
                        <?= $currentDate = date('d-m-y') ?></a>
                </li>

                <li class="ml-auto nav-item">

                    @php
                        use App\Models\Warehouse;
                        $branchs = Warehouse::all();
                    @endphp
                    <a class="text-white nav-link" href="#">
                        @foreach ($branchs as $branch)
                            @if ($branch->id == auth()->user()->level)
                                {{ $branch->name }}
                            @endif
                        @endforeach
                        {{-- {{ auth()->user()->level }} --}}
                    </a>

                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="ml-auto navbar-nav">


                <div class="btn-group">
                    <button type="button" class="text-white btn dropdown-toggle" data-toggle="dropdown"
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
        @include('layouts.sidebar') <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">

                <section class="content-header">
                    <div class="container-fluid">
                        <div class="mb-2 row">
                            <div class="col-sm-6">
                                <h1>Profit</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Profit
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>



                <div class="ml-2 container-fluid">

                    <!-- left column -->

                    <!-- general form elements -->

                    <!-- /.modal -->
                    <div class="row">
                        <a href="{{ url('report') }}" class="mx-1 ml-3 btn btn-primary">Invoices</a>
                        <a href="{{ url('report_item') }}" class="mx-1 btn btn-primary ">Items</a>
                        <a href="{{ url('report_expense') }}" class="mx-1 btn btn-primary ">Expenses</a>
                        @if (auth()->user()->type != 'Branch Manager')
                            <a href="{{ url('doctor') }}" class="mx-1 ml-3 btn btn-primary">Doctors</a>
                            <a href="{{ url('profit') }}" class="mx-1 ml-3 btn btn-primary">Profit</a>
                        @endif

                    </div>
                    <div class="my-5 container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <form action="{{ route('profitSearch') }}" method="get">
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label for="start_date">Date From:</label>
                                            <input type="date" name="start_date" class="form-control"
                                                value="{{ old('start_date', isset($start_date) ? $start_date : '') }}"
                                                required>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="end_date">Date To:</label>
                                            <input type="date" name="end_date" class="form-control"
                                                value="{{ old('end_date', isset($end_date) ? $end_date : '') }}"
                                                required>
                                        </div>
                                        @if (auth()->user()->is_admin == '1' || Auth::user()->type == 'Admin')
                                            <div class="col-md-4 form-group">
                                                <label for="branch">Branch:</label>
                                                <select name="branch" id="branch" class="form-control">
                                                    <option value="">All</option>
                                                    @foreach ($branch_drop as $drop)
                                                        <option value="{{ $drop->id }}"
                                                            {{ old('branch', isset($branch) ? $branch : '') == $drop->id ? 'selected' : '' }}>
                                                            {{ $drop->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @else
                                            <div class="col-md-4 form-group" style="display: none;">
                                                <label for="branch">Branch:</label>
                                                <select name="branch" id="branch" class="form-control">
                                                    @foreach ($branch_drop as $drop)
                                                        @if ($drop->id == auth()->user()->level)
                                                            <option value="{{ $drop->id }}">
                                                                {{ $drop->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        <div class="col-md-3 form-group">
                                            <input type="submit" class="btn btn-primary form-control" value="Search"
                                                style="background-color: #218838">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 col-md-12">
                        <div class="card ">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Profit Report</h3>
                                <div class="dropdown ml-auto mr-5">
                                    <!-- Dropdown Menu HTML -->
                                    @if (auth()->user()->is_admin == '1' || Auth::user()->type == 'Admin')
                                        <div id="branchDropdown" class="dropdown ml-auto"
                                            style="display:inline-block; margin-left: 10px;">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                {{ $currentBranchName }}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a href="{{ route('profit') }}" class="dropdown-item">All
                                                    Branches</a>
                                                @foreach ($branch_drop as $drop)
                                                    <a class="dropdown-item"
                                                        href="{{ route('profit', ['branch' => $drop->id]) }}">{{ $drop->name }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            </div>

                            <!-- /.card-header -->
                            <div class="card-body">

                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Date</th>
                                            <th>Invoice Qty.</th>
                                            <th>Total Income</th>
                                            <th>Lab</th>
                                            <th>Doctor Commission</th>
                                            <th>Total Expense</th>
                                            <th>Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Loop --}}
                                        @foreach ($reports as $key => $report)
                                            <tr>
                                                <th>{{ $key + 1 }}</th>
                                                <th>{{ $report->month }}</th>
                                                <th>{{ $report->qty }}</th>
                                                <th>{{ number_format($report->total_income) }}
                                                </th>
                                                <th>{{ number_format($report->total_buy_amount) }}</th>
                                                <th>{{ number_format($report->commission) }}</th>
                                                <th>{{ number_format($report->total_expense_amount ?? 'N\A') }}</th>
                                                <th>{{ number_format($report->total_income - $report->total_buy_amount - $report->commission) }}
                                                </th>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
        </div>

        </section>

    </div>



    </div>
    <script src="{{ asset('plugins/jquery/jquery.min.js ') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js ') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js ') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js ') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>



    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
                "buttons": [{
                        extend: 'excelHtml5',
                        text: 'Excel',
                        filename: 'report_profits', // Set filename here
                    },
                    {
                        extend: 'pdfHtml5',
                        text: ' PDF'
                    }
                ]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>



</body>

</html>
