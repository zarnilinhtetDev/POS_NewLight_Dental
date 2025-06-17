@include('layouts.header')

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

                <li class="nav-item text-white">
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
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">

                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Customer Edit</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Customer Edit
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>


                <div class="container-fluid mt-3">
                    <div class="row  justify-content-center d-flex">
                        <!-- left column -->
                        <div class="col-md-8">
                            <!-- general form elements -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title  " style="font-weight: bold;">PO Payment Edit</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">

                                    <form action="{{ route('po_payment_update', $make_payments->id) }}" method="POST"
                                        class="p-5">
                                        @csrf
                                        <div class="row mb-4">

                                            <div class="col-md-4 form-group">
                                                <button type="button" class="btn btn-primary" style="width: 100%">Total
                                                    -
                                                    {{ number_format($po->total) }}
                                                </button>

                                            </div>
                                            <input type="hidden" id="location" value="{{ $make_payments->branch }}">

                                            <div class="col-md-4 form-group">
                                                <button type="button" class="btn btn-primary"
                                                    style="width: 100%">Deposit
                                                    -
                                                    {{ number_format($po->deposit) }}
                                                </button>

                                            </div>

                                            <div class="col-md-4 form-group">
                                                <input type="hidden" name="remain_balance"
                                                    value="{{ $make_payments->remain_balance }}" id="remain_balance">
                                                <button type="button" class="btn btn-primary" style="width: 100%">
                                                    Remaining Balance -
                                                    {{ number_format($po->remain_balance) }}
                                                </button>
                                            </div>


                                            <div class="form-group col-md-6">
                                                <label for="">PO No</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $make_payments->po_no ?? '' }}" name="po_no" readonly>

                                                {{-- <input type="text" class="form-control" id="payment_id"
                                                    value="{{ $make_payments->branch }}" name="" hidden> --}}
                                                <input type="hidden" class="form-control" id="location" name="branch"
                                                    value="{{ $make_payments->branch }}">
                                            </div>



                                            <div class="form-group col-md-6">
                                                <label for="payment_date">Payment Date<span
                                                        class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="payment_date"
                                                    placeholder="Enter payment date" name="payment_date" required
                                                    value="{{ $make_payments->payment_date }}">

                                            </div>



                                            <div class="form-group col-md-6" id="total_amount">
                                                <label for="amount">Amount <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="amount" name="amount"
                                                    placeholder="Enter amount" value="{{ $make_payments->amount }}"
                                                    required oninput="validateAmount()">
                                            </div>

                                            <div class="form-group col-md-12">
                                                <label for="note">Note</label>
                                                <textarea rows="3" class="form-control" id="note" name="note" placeholder="Enter note">{{ $make_payments->note }}</textarea>
                                            </div>






                                        </div>
                                        <div class="modal-footer justify-content-end">
                                            {{-- <a href="{{ url('make_payment', $payment->id) }}"
                                                class="btn btn-danger">Back</a> --}}
                                            <button type="button" class="btn btn-danger"
                                                onclick="history.back();">Back</button>

                                            <button type="submit" class="btn btn-primary">Update </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>


        </section>

    </div>



    </div>


    @include('layouts.footer')
