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
        @include('layouts.sidebar')
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">

                <section class="content-header">
                    <div class="container-fluid">
                        <div class="mb-2 row">
                            <div class="col-sm-6">
                                <h1>Treatement Register</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Treatement Register
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

            </section>
            <div class="content-body">
                <div class="container-fluid justify-content-center d-flex">
                    <div class="card card-default col-md-12">
                        <div class="card-header">
                            <h3 class="card-title">Treatement Register</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ url('item_store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">



                                <div class="mt-4 row">
                                    <div class="form-group col-md-6">
                                        <label for="item_name">Treatement Name <span class="text-danger">
                                                *</span></label>
                                        <input type="text" class="form-control" id="item_name" name="item_name"
                                            placeholder="Enter Item Name" value="{{ old('item_name') }}" required>
                                    </div>



                                    <div class="form-group col-md-6">
                                        <label for="descriptions">Treatement Descriptions</label>
                                        <input type="text" class="form-control" id="descriptions"
                                            placeholder="Enter Item Descriptions" name="descriptions"
                                            value="{{ old('descriptions') }}">
                                    </div>




                                    {{-- @if (auth()->user()->is_admin == '1' || Auth::user()->type == 'Admin')
                                        <div class="form-group col-md-6">
                                            <label for="warehouse_id">Location<span class="text-danger">*</span></label>
                                            <input type="hidden" id="warehouse_id_from" name="warehouse_id_from">
                                            <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                                                <option value="" selected disabled>Select Location</option>
                                                @foreach ($branchs as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <div class="form-group col-md-6" style="display: none;">
                                            <label for="warehouse_id">Location<span class="text-danger">*</span></label>
                                            <input type="hidden" id="warehouse_id_from" name="warehouse_id_from">
                                            <input type="hidden" id="warehouse_id" name="warehouse_id"
                                                class="form-control" value="{{ auth()->user()->level }}" readonly>
                                        </div>
                                    @endif --}}

                                </div>


                                <hr>


                                <div class="mt-3 row">


                                    <div class="form-group col-md-6">
                                        <label for="quantity">
                                            Quantity
                                        </label>
                                        <input type="number" class="form-control" name="quantity" id="quantity"
                                            value="0" placeholder="Enter Quantity">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="item_unit">
                                            Category <span class="text-danger">*</span>
                                        </label>
                                        <select name="item_unit" id="item_unit" class="form-control" required>
                                            <option selected disabled>Select Category</option>
                                            <option id="Service" value="Service">Service</option>
                                            <option id="Lab" value="Lab">Lab</option>
                                            <option id="Clinic" value="Clinic">Clinic</option>
                                        </select>
                                        @error('item_unit')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>



                                    {{-- <div class="form-group col-md-4">
                                        <label for="reorder_level_stock">Reorder Level Stock</label>
                                        <input type="number" class="form-control" id="reorder_level_stock"
                                            name="reorder_level_stock" value="{{ old('reorder_level_stock') }}"
                                    placeholder="Enter Reorder Level Stock">
                                </div> --}}



                                </div>

                                <hr>

                                <div class="row" id="lab_card">
                                    <div class="form-group col-md-6">
                                        <label for="sale_price">Buy Price</label>
                                        <input type="number" class="form-control" id="sale_price" name="sale_price"
                                            placeholder="Enter Retail Price" value="{{ old('sale_price') }}">
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label for="buy_price">Sale Price</label>
                                        <input type="number" class="form-control" id="buy_price" name="buy_price"
                                            placeholder="Enter Buy Price" value="{{ old('buy_price') }}">
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6" id="service_card">
                                        <label for="buy_price">Sale Price</label>
                                        <input type="number" class="form-control" id="service_buy_price"
                                            name="service_buy_price" placeholder="Enter Retail Price"
                                            value="{{ old('service_buy_price') }}">
                                    </div>
                                </div>

                            </div>
                            <!-- /.card-body -->

                            <div class="mb-3 ml-3 ">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>



    </div>


    @include('layouts.footer')

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script> -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.option-checkbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.option-checkbox').not(this).prop('checked', false);
                }
            });


            var path = "{{ url('item_search') }}";
            $('#customer').typeahead({
                source: function(query, process) {
                    return $.get(path, {
                        query: query
                    }, function(data) {
                        console.log("Data received:", data); // Log the received data
                        return process(data);
                    })
                }
            })
            $(document).on('click', '#customer_search', function(e) {
                e.preventDefault();
                let serialNumber = $("#customer").val();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('item_data_search_fill') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        model: serialNumber // Adjusted to match server-side parameter name
                    },
                    success: function(data) {
                        console.log(data);
                        $("#item_name").val(data['item']['item_name']);
                        $("#barcode").val(data['item']['barcode']);
                        $("#descriptions").val(data['item']['descriptions']);
                        $("#expired_date").val(data['item']['expired_date']);
                        $("#category").val(data['item']['category']);
                        $("#quantity").val(data['item']['quantity']);
                        $("#item_unit").val(data['item']['item_unit']);
                        // $("#price").val(data['item']['price']);
                        $("#reorder_level_stock").val(data['item']['reorder_level_stock']);
                        $("#mingalar_market").val(data['item']['mingalar_market']);
                        $("#company_price").val(data['item']['company_price']);
                        $("#other").val(data['item']['other']);
                        $("#retail_price").val(data['item']['retail_price']);
                        $("#wholesale_price").val(data['item']['wholesale_price']);
                        $("#buy_price").val(data['item']['buy_price']);
                        // $("#warehouse_id").val(data['warehouse']['id']);

                        // Find the option with the selected value and hide it
                        $("#warehouse_id option[value='" + data['warehouse']['id'] + "']")
                            .hide();

                        // Adjusted to match server-side data
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>

    <script>
        document.getElementById("show").addEventListener("change", function() {
            var additionalElements = document.getElementById("additional-elements");
            if (this.checked) {
                additionalElements.style.display = "block";
            } else {
                additionalElements.style.display = "none";
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#item_unit').change(function() {
                // let lab_card = $("#lab_card");
                var selectedValue = $(this).val();

                if (selectedValue === 'Service') {
                    $('#lab_card').hide();
                    $('#sale_price').val("");
                    $('#buy_price').val("");
                    $('#service_card').show();
                } else if (selectedValue === 'Lab') {
                    $('#lab_card').show();
                    $('#service_buy_price').val("");
                    $('#service_card').hide();
                } else {
                    $('#lab_card').show();
                    $('#service_buy_price').val("");
                    $('#service_card').hide();
                }
            });
            $('#item_unit').trigger('change');
        });
    </script>
