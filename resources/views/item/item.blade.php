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
                    <a class="text-white nav-link " href="#">Date -
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
        @include('layouts.sidebar')
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">

                <section class="content-header">
                    <div class="container-fluid">
                        <div class="mb-2 row">
                            <div class="col-sm-6">
                                <h1>Items</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>

                                    </li>
                                    <li class="breadcrumb-item">Items</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                @if (session('delete'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ session('delete') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif


                @if (session('excelimport'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session('excelimport') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span dangeraria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ session('error') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @elseif (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session('success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if ($errors->has('file'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error:</strong> {{ $errors->first('file') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @php
                    $userPermissions = [];
                    if (auth()->user()->permission) {
                        $decodedPermissions = json_decode(auth()->user()->permission, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $userPermissions = $decodedPermissions;
                        }
                    }
                @endphp


                <div class="container-fluid">
                    <div class="ml-2 row d-flex">
                        <form action="{{ route('file-import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4 form-group" style="max-width: 500px; margin: 0 auto;">

                                <div class="text-left custom-file">


                                    <label for="warehouse">Choose Location</label>
                                    <select name="warehouse_id" id="warehouse" class="form-control" required>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>


                                    <div class="p-1 mt-2 text-left custom-file col"
                                        style="border:#d0d0db 1px solid;background-color: white">
                                        <input type="file" name="file" class="" id="customFile">
                                    </div>
                                    <button class="mt-3 btn btn-primary">Import </button>

                                    <a class="mt-3 btn btn-success" href="{{ route('file-export') }}">Export </a>
                                </div>
                            </div>

                            <a class="" href="{{ route('file-import-template') }}">Download
                                Import CSV Template</a>

                        </form>
                    </div>

                    <div class="mt-5 mr-auto col">
                        <a href="{{ url('items_register') }}" type="button" class="mr-auto btn btn-primary ">
                            Treatement Register</a>
                    </div>


                    <div class="container-fluid">

                        <!-- /.modal -->
                        <div class="mt-3 col-md-12">
                            <div class="bg-white">

                                <div class="card-header">
                                    <h3 class="card-title">Treatement List</h3>
                                </div>

                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="example1"
                                        class="table table-bordered table-striped table-responsive-lg">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Treatement Name</th>
                                                <th>Category</th>
                                                {{-- <th>Location</th> --}}
                                                <th>Buy Price</th>
                                                <th>Sale Price</th>

                                                <th style="background-color: rgb(221, 215, 215)">Quantity</th>

                                                <th style="width: 15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = '1';
                                            @endphp

                                            @foreach ($items as $item)
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>{{ $item->item_name }}</td>
                                                    <td>{{ $item->item_unit }}</td>
                                                    {{-- <td>{{ $item->warehouse->name ?? 'N/A' }}</td> --}}
                                                    <td>{{ $item->sale_price ?? 0 }}</td>

                                                    <td>{{ $item->buy_price ?? $item->service_buy_price }}</td>
                                                    <td style="background-color: rgb(221, 215, 215)">
                                                        {{ $item->quantity }}
                                                    </td>

                                                    <td>


                                                        @if (in_array('Treatment Details', $userPermissions) || auth()->user()->is_admin == '1')
                                                            <a href="{{ url('item_details', $item->id) }}"
                                                                class="btn btn-primary btn-sm"><i
                                                                    class="fa-solid fa-eye"></i></a>
                                                        @endif

                                                        @if (in_array('Treatment Edit', $userPermissions) || auth()->user()->is_admin == '1')
                                                            <a href="{{ url('item_edit', $item->id) }}"
                                                                class="btn btn-success btn-sm"><i
                                                                    class="fa-solid fa-pen-to-square"></i></a>
                                                        @endif


                                                        @if (in_array('Treatment Delete', $userPermissions) || auth()->user()->is_admin == '1')
                                                            <a href="{{ url('item_delete', $item->id) }}"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Are you sure you want to delete this Item ?')"><i
                                                                    class="fa-solid fa-trash"></i></a>
                                                        @endif


                                                        @if (in_array('Treatment In/Out', $userPermissions) || auth()->user()->is_admin == '1')
                                                            <a href="{{ url('in_out', $item->id) }}"
                                                                class="mt-1 btn btn-info btn-sm">In/Out
                                                                History </a>
                                                        @endif

                                                    </td>
                                                </tr>
                                                @php
                                                    $no++;
                                                @endphp
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
    <!-- AdminLTE for demo purposes -->

    <!-- Page specific script -->
    <script>
        new DataTable('#example1', {

            "lengthChange": false,
            "paging": true,
            "pageLength": 100,

        });
    </script>
</body>

</html>
