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
                    <a class="nav-link text-white" href="#">
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
                                <h1>Clinic Items Reports</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Clinic Items Reports
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
                    {{-- <div class="row">
                        <a href="{{ url('report') }}" class="mx-1 ml-3 btn btn-primary">Invoices</a>
                        <a href="{{ url('report_item') }}" class="mx-1 ml-3 btn btn-primary ">Items</a>
                        <a href="{{ url('report_expense') }}" class="mx-1 ml-3 btn btn-primary ">Expenses</a>
                        @if (auth()->user()->type != 'Branch Manager')
                            <a href="{{ url('doctor') }}" class="mx-1 ml-3 btn btn-primary">Doctors</a>
                            <a href="{{ url('profit') }}" class="mx-1 ml-3 btn btn-primary">Profit</a>
                        @endif

                    </div> --}}
                    <div class="my-5 container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <form action="{{ url('clinic_item_search') }}" method="get">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-5 form-group">
                                            <label for="start_date">Date From :</label>
                                            <input type="date" name="start_date" class="form-control" required>
                                        </div>
                                        <div class="col-md-5 form-group">
                                            <label for="end_date">Date To :</label>
                                            <input type="date" name="end_date" class="form-control" required>
                                        </div>
                                        <div class="mt-3 col-md-3 form-group">
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
                            <div class="card-header">
                                <h3 class="card-title">Clinic Item Report</h3>
                            </div>

                            <!-- /.card-header -->
                            <div class="card-body">

                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <th>Date</th>
                                            <th>Sale Price</th>
                                            <th>Buy Price</th>
                                            <th>Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = '1';
                                            $totalProfit = 0;
                                        @endphp
                                        @if (!empty($search_items))
                                            @foreach ($search_items as $item)
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>{{ $item->item_name }}</td>
                                                    <td>{{ $item->item_unit }}</td>
                                                    <td>{{ $item->descriptions }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ $item->created_at->format('d M Y') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($item->buy_price ?? $item->service_buy_price) }}

                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($item->sale_price ?? '0') }}</td>
                                                    <td class="text-right">
                                                        @if (isset($item->buy_price) || isset($item->service_buy_price))
                                                            @php
                                                                $buyPrice =
                                                                    $item->buy_price ?? $item->service_buy_price;
                                                                $salePrice = $item->sale_price ?? null;
                                                                $profit =
                                                                    $salePrice !== null
                                                                        ? $buyPrice - $salePrice
                                                                        : $buyPrice;
                                                            @endphp

                                                            {{ number_format($profit, 2, '.', ',') }}

                                                            @php
                                                                $totalProfit += $profit;
                                                            @endphp
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach
                                        @else
                                            @foreach ($items as $item)
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>{{ $item->item_name }}</td>
                                                    <td>{{ $item->item_unit }}</td>
                                                    <td>{{ $item->descriptions }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ $item->created_at->format('d M Y') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($item->buy_price ?? $item->service_buy_price) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($item->sale_price ?? '0') }}</td>

                                                    <td class="text-right">
                                                        @if (isset($item->buy_price) || isset($item->service_buy_price))
                                                            @php
                                                                $buyPrice =
                                                                    $item->buy_price ?? $item->service_buy_price;
                                                                $salePrice = $item->sale_price ?? null;
                                                                $profit =
                                                                    $salePrice !== null
                                                                        ? $buyPrice - $salePrice
                                                                        : $buyPrice;
                                                            @endphp

                                                            {{ number_format($profit) }}

                                                            @php
                                                                $totalProfit += $profit;
                                                            @endphp
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach
                                        @endif

                                    <tfoot>
                                        <tr>
                                            <td colspan="6" style="text-align:right">Total</td>
                                            <td colspan="" class="text-right">
                                                @if (!empty($search_items))
                                                    {{ number_format($search_total) }}
                                                @else
                                                    {{ number_format($total) }}
                                                @endif
                                            </td>
                                            <td colspan="" class="text-right">
                                                @if (!empty($search_items))
                                                    {{ number_format($search_total_2) }}
                                                @else
                                                    {{ number_format($total_2) }}
                                                @endif
                                            </td>
                                            <td class="text-right"> {{ number_format($totalProfit) }}</td>
                                        </tr>
                                    </tfoot>
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
                        filename: 'report_items', // Set filename here
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
