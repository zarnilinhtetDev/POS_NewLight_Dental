<!DOCTYPE html>
<HTML>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        input {
            position: relative;
            width: 150px;
            height: 40px;
            color: white;
        }

        input:before {
            position: absolute;
            top: 6px;
            left: 6px;
            content: attr(data-date);
            display: inline-block;
            color: black;
        }

        input::-webkit-datetime-edit,
        input::-webkit-inner-spin-button,
        input::-webkit-clear-button {
            display: none;
        }

        input::-webkit-calendar-picker-indicator {
            position: absolute;
            top: 6px;
            right: 0;
            color: black;
            opacity: 1;
        }
    </style>

</head>

<body>

    <div class="container-fluid" id="content">


        <h1 class="mx-4 mt-3">
            Invoice
        </h1>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ session('success') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif



        <div class="modal fade" id="modal-lg">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"> Register New Patient</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('patient_register') }}" method="POST">
                            @csrf
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="" placeholder="Enter Name"
                                        required autofocus name="name">
                                </div>


                                <div class="form-group">
                                    <label for="phno">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id=""
                                        placeholder="Enter Phone Number" name="phno" required>
                                </div>

                                <div class="form-group">
                                    <label for="age">Age <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="" placeholder="Enter Age"
                                        name="age" required>
                                </div>

                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id=""
                                        placeholder="Enter Address" name="address" required>
                                </div>

                                @php
                                    $warehousePermission = auth()->user()->level
                                        ? json_decode(auth()->user()->level, true)
                                        : [];
                                @endphp

                                {{-- @if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin')
                                    <div class="form-group">
                                        <label for="branch">Location<span class="text-danger">*</span></label>
                                        <select name="branch" id="" class="form-control" required>
                                            <option selected disabled>Select
                                                Location</option>
                                            @foreach ($warehouses as $branch)
                                                <option value="{{ $branch->id }}">
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="form-group" style="display: none;">
                                        <label for="branch">Location<span class="text-danger">*</span></label>

                                        <select name="branch" id="" class="form-control" required>
                                            @foreach ($warehouses as $branch)
                                                @if (in_array($branch->id, $warehousePermission))
                                                    <option value="{{ $branch->id }}">
                                                        {{ $branch->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                @endif --}}

                            </div>



                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save
                        </button>
                    </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <form method="post" id="myForm" action="{{ url('invoice_register') }}" enctype="multipart/form-data">
            @csrf

            <div class="mx-3">

                <div class="my-3 mt-4 row">
                    <div class="col-md-3">
                        <label for="invoice_no" style="font-weight:bolder">Invoice Number</label>
                        <input type="text" id="invoice_no" class="form-control" name="invoice_no"
                            value="{{ $invoice_no }}" readonly>
                    </div>





                    <div class="col-md-3">
                        <label for="invoice_date" style="font-weight:bolder">Date</label>
                        <input type="date" name="invoice_date" class="form-control" max="{{ date('Y-m-d') }}"
                            value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="payment_method" style="font-weight:bolder">{{ trans('Payment Methods') }}</label>
                        <select class="mb-4 form-control round" required aria-label="Default select example"
                            name="payment_method">
                            <option value="Cash">Cash</option>
                            <option value="KBZ Pay">KBZ Pay</option>
                            <option value="CB Pay">CB Pay</option>
                            <option value="Others">Others</option>

                        </select>
                    </div>

                    {{-- <div class="col-md-3">
                        @if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin')
                            <div class="form-group">
                                <label for="branch">Location<span class="text-danger">*</span></label>
                                <select name="branch" id="branch" class="form-control" required>
                                    <option selected disabled>Select Location</option>
                                    @foreach ($warehouses as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="form-group">
                                <label for="branch">Location<span class="text-danger">*</span></label>
                                <select name="branch" id="" class="form-control" required>
                                    @foreach ($warehouses as $branch)
                                        @if (in_array($branch->id, $warehousePermission))
                                            <option value="{{ $branch->id }}">
                                                {{ $branch->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div> --}}

                    <input type="hidden" name="quote_category" id="quote_category" value="Invoice">
                </div>
                <hr>
                <div class="content-wrapper">
                    <div class="content-body">
                        <!-- <div class=""> -->
                        <div class="card-content">

                            <div class="card-body p-0">

                                <div class="row">
                                    <div class="col-sm-12 cmp-pnl">

                                        <div class="row">
                                            <div class="frmSearch col-sm-2">
                                                <span style="font-weight:bolder">
                                                    <label for="cst"
                                                        class="caption">{{ trans('Search  Patient Name') }}</label>
                                                </span>
                                                <div class="form-group d-flex">
                                                    <input type="text" id="customer" name="customer"
                                                        class="mr-2 form-control round" autocomplete="off"
                                                        placeholder="Search.....">
                                                    &nbsp;&nbsp;&nbsp; <button type="submit" class="btn btn-primary"
                                                        id="customer_search">Add</button>
                                                </div>
                                                <div id="customer-box-result"></div>
                                            </div>

                                            <div class="frmSearch col-sm-2">
                                                <span style="font-weight:bolder">
                                                    <label for="cst"
                                                        class="caption">{{ trans('Search Patient Phone No') }}</label>
                                                </span>
                                                <div class="form-group d-flex">
                                                    <input type="text" id="customer_phone" name="customer_phone"
                                                        class="mr-2 form-control round" autocomplete="off"
                                                        placeholder="Search.....">
                                                    &nbsp;&nbsp;&nbsp; <button type="submit" class="btn btn-primary"
                                                        id="customer_phone_search">Add</button>
                                                </div>
                                                <div id="customer-box-result"></div>
                                            </div>

                                            <div class="col-md-2 mt-4">
                                                <button type="button" data-toggle="modal" data-target="#modal-lg"
                                                    class="btn btn-secondary">Patient
                                                    Register</button>
                                            </div>


                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="doctor" style="font-weight:bolder">Doctor Name <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <select name="doctor_id" id="doctor_id" class="form-control"
                                                        required>
                                                        <option value="">Select Doctor</option>
                                                        @foreach ($doctors as $doctor)
                                                            <option data-branch="{{ $doctor->branch }}"
                                                                value="{{ $doctor->id }}">
                                                                {{ $doctor->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if (session('doctor_id'))
                                                        <strong
                                                            class="text-danger">{{ session('doctor_id') }}</strong>
                                                    @endif
                                                </div>
                                            </div>




                                            @if (auth()->user()->is_admin == '1')
                                                <div class="col-md-3">
                                                    <label for="location" style="font-weight:bolder">
                                                        Location<span class="text-danger fw-bold">*</span></label>
                                                    <select name="branch" id="location" class="form-control"
                                                        required>
                                                        <option value="">Select Location</option>
                                                        @foreach ($warehouses as $warehouse)
                                                            <option value="{{ $warehouse->id }}">
                                                                {{ $warehouse->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <div class="col-md-3">
                                                    <label for="location" style="font-weight:bolder">Location<span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <select name="branch" id="location" class="form-control"
                                                        required>
                                                        <option value="">Select Location</option>
                                                        @foreach ($warehouses as $branch)
                                                            @if (in_array($branch->id, $warehousePermission))
                                                                <option value="{{ $branch->id }}">
                                                                    {{ $branch->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif

                                            {{-- <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="doctor" style="font-weight:bolder">Doctor Name <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <select name="doctor_id" id="doctor_id" class="form-control"
                                                        required>
                                                        <option value="">Select Doctor</option>
                                                        @if ($selectedBranch)
                                                            @foreach ($doctors as $doctor)
                                                                <option data-branch="{{ $doctor->branch }}"
                                                                    value="{{ $doctor->id }}">
                                                                    {{ $doctor->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @if (session('doctor_id'))
                                                        <strong
                                                            class="text-danger">{{ session('doctor_id') }}</strong>
                                                    @endif
                                                </div>
                                            </div>

                                            @if (auth()->user()->is_admin == '1')
                                                <div class="col-md-3">
                                                    <label for="location" style="font-weight:bolder">
                                                        Location<span class="text-danger fw-bold">*</span></label>
                                                    <select name="location" id="location_admin" class="form-control"
                                                        required>
                                                        <option value="">Select Location</option>
                                                        @foreach ($warehouses as $warehouse)
                                                            <option value="{{ $warehouse->id }}">
                                                                {{ $warehouse->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <div class="col-md-3">
                                                    <label for="location" style="font-weight:bolder">Location<span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <select name="location" id="location_user" class="form-control"
                                                        required>
                                                        <option value="">Select Location</option>
                                                        @foreach ($warehouses as $branch)
                                                            @if (in_array($branch->id, $warehousePermission))
                                                                <option value="{{ $branch->id }}">
                                                                    {{ $branch->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif --}}


                                        </div>





                                        <div class="frmSearch col-sm-12">
                                            <input type='hidden' name='customer_id' id="customer_id"
                                                class="form-control">
                                            <input type='hidden' name='status' id="status" class="form-control"
                                                value="invoice">

                                            <div class="row mt-3">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="customer" style="font-weight:bolder">Patient
                                                            Name</label>
                                                        <input type="text" id="patient" name="name"
                                                            class="form-control round" autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="phone_no" style="font-weight:bolder">Phone
                                                            Number</label>
                                                        <input type="text" id="phone_no" name="phno"
                                                            class="form-control round" autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="age" style="font-weight:bolder">Age</label>
                                                        <input type='number' name='age' class="form-control"
                                                            id="age">
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="address"
                                                            style="font-weight:bolder">Address</label>
                                                        <input type='text' name='address' class="form-control"
                                                            id="address">
                                                    </div>
                                                </div>

                                            </div>

                                            <div id="customer-box-result"></div>

                                        </div>


                                    </div>
                                    <div class="col-sm-12 mt-3">
                                        <div class="row table-responsive " style="margin-top:1vh;">
                                            <!-- <table class="table-responsive tfr my_stripe"> -->
                                            <table class="table table-bordered">
                                                <thead style="background-color:#0047AA;color:white;">
                                                    <tr class="item_header bg-gradient-directional-blue white"
                                                        style="margin-bottom:10px;">
                                                        <th width="5%" class="text-center">
                                                            {{ trans('No') }}
                                                        </th>
                                                        <th width="18%" class="text-center">
                                                            {{ trans('Treatment Name') }}
                                                        </th>
                                                        <th width="23%" class="text-center">
                                                            {{ trans('Descriptions') }}
                                                        </th>
                                                        <th width="8%" class="text-center">
                                                            {{ trans('Qty') }}
                                                        </th>
                                                        <th width="12%" class="text-center">
                                                            {{ trans('Category') }}
                                                        </th>

                                                        <th width="9%" class="text-center">
                                                            {{ trans(' Price') }}
                                                        </th>

                                                        <th width="14%" class="text-center">
                                                            {{ trans('Amount') }}
                                                            ({{ config('currency.symbol') }})
                                                        </th>

                                                    </tr>

                                                </thead>

                                                <tbody id="showitem123">
                                                    <tr>


                                                        <td class="text-center" id="count">1</td>
                                                        <td><input type="text"
                                                                class="form-control productname typeahead"
                                                                name="part_number[]" value="{{ old('part_number') }}"
                                                                placeholder="{{ trans('Enter Part Number') }}"
                                                                id='productname-0' autocomplete="off">
                                                        </td>

                                                        <td><input type="text"
                                                                class="form-control description typeahead"
                                                                value="{{ old('part_description') }}"
                                                                name="part_description[]"
                                                                placeholder="{{ trans('') }}" id='description-0'
                                                                autocomplete="off"></td>
                                                        <td><input type="text" class="form-control req amnt"
                                                                name="product_qty[]" id="amount-0"
                                                                autocomplete="off" value="1"><input
                                                                type="hidden" id="alert-0" value=""
                                                                name="alert[]"></td>
                                                        <td>

                                                            <input type="text" name="category[]" id="category-0"
                                                                class="form-control category">
                                                        </td>
                                                        <td><input type="text" class="form-control buy_price"
                                                                name="buy_price[]" id="buy_price-0"
                                                                autocomplete="off" value="0">
                                                        </td>
                                                        <td style="display: none;"><input type="hidden"
                                                                class="form-control sale_price" name="sale_price[]"
                                                                id="sale_price-0" autocomplete="off" value="0">
                                                        </td>
                                                        <td style="display : none;"><input type="text"
                                                                class="form-control warehouse" name="warehouse[]"
                                                                id="warehouse-0" autocomplete="off"></td>
                                                        <td style="text-align:center">
                                                            <span class='ttlText' id="foc-0"></span>
                                                            <span
                                                                class="currenty">{{ config('currency.symbol') }}</span>
                                                            <strong>
                                                                <span class='ttlText' id="result-0"></span>
                                                            </strong>
                                                        </td>
                                                        <input type="hidden" class="form-control vat "
                                                            name="product_tax[]" id="vat-0" value="0">
                                                        <input type="hidden" name="total_tax[]" id="taxa-0"
                                                            value="0">
                                                        <input type="hidden" class="ttInput"
                                                            name="product_subtotal[]" id="total-0" value="0">
                                                        <input type="hidden" class="pdIn" name="product_id[]"
                                                            id="pid-0" value="0">
                                                        <input type="hidden" attr-org="" name="unit[]"
                                                            id="unit-0" value="">
                                                        <input type="hidden" name="unit_m[]" id="unit_m-0"
                                                            value="1">
                                                        <input type="hidden" name="code[]" id="hsn-0"
                                                            value="">
                                                        <input type="hidden" name="serial[]" id="serial-0"
                                                            value="">
                                                    </tr>
                                                </tbody>

                                            </table>

                                            <table class="mt-3">
                                                <tbody id="showitem">

                                                    <tr style="display: table-row;">
                                                        <td></td>
                                                        <td colspan="">

                                                        </td>

                                                    </tr>

                                                    <tr class="last-item-row sub_c">
                                                        <td></td>
                                                        <td class="add-row">
                                                            <button type="button" class="btn btn-success"
                                                                id="addproduct"
                                                                style="margin-top:20px;margin-bottom:20px;">
                                                                <i class="fa fa-plus-square"></i>
                                                                {{ trans('Add row') }}
                                                            </button>
                                                            <button type="button" class="btn btn-primary"
                                                                id="calculate">
                                                                Calculate
                                                            </button>

                                                            <a href="{{ URL('items') }}" target="_blank"
                                                                id="item_search">
                                                                <button type="button" class="btn btn-success">
                                                                    <i class="fa fa-plus-square"></i> Item Search
                                                                </button></a>

                                                        </td>
                                                        <td colspan="6"></td>
                                                        <br><br>

                                                    </tr>

                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td colspan="2">
                                                            @if (isset($employees[0]))
                                                                {{ trans('general.employee') }}
                                                                <select name="user_id"
                                                                    class="selectpicker form-control">
                                                                    <option value="{{ $logged_in_user->id }}">
                                                                        {{ $logged_in_user->first_name }}
                                                                    </option>
                                                                    @foreach ($employees as $employee)
                                                                        <option value="{{ $employee->id }}">
                                                                            {{ $employee->first_name }}
                                                                            {{ $employee->last_name }}
                                                                        </option>
                                                                    @endforeach

                                                                </select>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td>

                                                        </td>
                                                    </tr>
                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td colspan="2">

                                                        </td>
                                                        <td colspan="3" align="right"><strong>Sub Total
                                                            </strong>
                                                        </td>
                                                        <td align="left" colspan="2" class="col-md-4"><input
                                                                type="text" name="sub_total" class="form-control"
                                                                id="invoiceyoghtml" readonly
                                                                style="background-color: #E9ECEF">

                                                        </td>

                                                    </tr>
                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td colspan="2">

                                                        </td>
                                                        <td colspan="3" align="right"><strong>Discount
                                                            </strong>
                                                        </td>
                                                        <td align="left" colspan="2" class="col-md-4"><input
                                                                type="text" name="discount" class="form-control"
                                                                id="total_discount">

                                                        </td>

                                                    </tr>
                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td colspan="2">

                                                        </td>
                                                        <td colspan="3" align="right"><strong>Total
                                                            </strong>
                                                        </td>
                                                        <td align="left" colspan="2" class="col-md-4"><input
                                                                type="text" name="total" class="form-control"
                                                                id="total_total" readonly
                                                                style="background-color: #E9ECEF">

                                                        </td>

                                                    </tr>
                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td colspan="2">

                                                        </td>
                                                        <td colspan="3" align="right"><strong>Deposit
                                                            </strong>
                                                        </td>
                                                        <td align="left" colspan="2"><input type="text"
                                                                name="paid" class="form-control" id="paid"
                                                                onchange="paidFunction()">

                                                        </td>

                                                    </tr>
                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td colspan="2">

                                                        </td>
                                                        <td colspan="3" align="right"><strong>Remaining
                                                                Balance
                                                            </strong>
                                                        </td>
                                                        <td align="left" colspan="2"><input type="text"
                                                                name="balance" class="form-control" id="balance"
                                                                readonly="">

                                                        </td>
                                                    </tr>

                                                    <tr class="sub_c " style="display: table-row;">
                                                        <td colspan="12"> <label for="remark">Remark</label>
                                                            <textarea name="remark" id="remark" class="form-control" rows="2"></textarea>

                                                        </td>
                                                    </tr>
                                                    <tr class="sub_c " style="display: table-row;">


                                                        <td align="right" colspan="9">

                                                            <button id="submitButton" class="mt-3 btn btn-primary"
                                                                type="submit">Save</button>


                                                            <a href="{{ url('invoice') }}" type="submit"
                                                                class="mt-3 btn btn-danger">Cancel
                                                            </a>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
        </form>

    </div>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    {{-- Customer Name Search --}}
    <script>
        // $(document).ready(function() {
        //     var path = "{{ route('customer_service_search') }}";
        //     $('#customer').typeahead({
        //         source: function(query, process) {
        //             var Selectedlocation = $('#branch').val();

        //             return $.get(path, {
        //                 query: query,
        //                 location: Selectedlocation,

        //             }, function(data) {
        //                 var formattedData = [];
        //                 $.each(data, function(index, customer) {
        //                     if (customer.name.toLowerCase().indexOf(query
        //                             .toLowerCase()) !== -1) {
        //                         formattedData.push(customer.name);
        //                     } else if (customer.phno.indexOf(query) !== -1) {
        //                         formattedData.push(customer.phno);
        //                     }
        //                 });
        //                 return process(formattedData);
        //             });
        //         }
        //     });

        // Debugging version
        $(document).ready(function() {
            $('#customer').typeahead({
                source: function(query, process) {
                    console.log("Current query:", query);

                    $.ajax({
                        url: "{{ route('customer_service_search') }}",
                        data: {
                            query: query,
                            location: $('#location').val()
                        },
                        success: function(data) {
                            console.log("Response data:", data);
                            var names = $.map(data, function(customer) {
                                return customer.name;
                            });
                            process(names);
                        },
                        error: function(xhr) {
                            console.error("Error:", xhr.responseText);
                        }
                    });
                },
                minLength: 1,
                items: 5 // Maximum items to show
            });
        });
        $(document).on('click', '#customer_search', function(e) {
            e.preventDefault();
            let serialNumber = $("#customer").val();
            $.ajax({
                type: 'POST',
                url: "{{ route('customer_service_search_fill') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    model: serialNumber
                },
                success: function(data) {
                    console.log(data);
                    $("#patient").val(data['customer']['name']);
                    $("#customer_id").val(data['customer']['id']);
                    $("#phone_no").val(data['customer']['phno']);
                    $("#type").val(data['customer']['type']);
                    $("#address").val(data['customer']['address']);
                    $("#age").val(data['customer']['age']);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    </script>

    {{-- Customer Phone Search --}}

    <script>
        $(document).ready(function() {
            var path = "{{ route('customer_phone_search') }}";

            $('#customer_phone').typeahead({
                source: function(query, process) {
                    var Selectedlocation = $('#location').val();

                    return $.get(path, {
                        query: query,
                        location: Selectedlocation,
                    }, function(data) {
                        var formattedData = [];
                        $.each(data, function(index, customer) {
                            if (customer.name.toLowerCase().indexOf(query
                                    .toLowerCase()) !== -1) {
                                formattedData.push(customer.name);
                            } else if (customer.phno.indexOf(query) !== -1) {
                                formattedData.push(customer.phno);
                            }
                        });
                        return process(formattedData);
                    });
                }
            });

            $(document).on('click', '#customer_phone_search', function(e) {
                e.preventDefault();

                let serialNumber = $("#customer_phone").val();
                let Selectedlocation = $('#location').val(); // location ကိုယူ

                $.ajax({
                    type: 'POST',
                    url: "{{ route('customer_phone_search_fill') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        model: serialNumber,
                        location: Selectedlocation // ဒီနေရာမှာပေးဖို့လိုတယ်
                    },
                    success: function(data) {
                        console.log(data);
                        $("#patient").val(data['customer']['name']);
                        $("#customer_id").val(data['customer']['id']);
                        $("#phone_no").val(data['customer']['phno']);
                        $("#type").val(data['customer']['type']);
                        $("#address").val(data['customer']['address']);
                        $("#age").val(data['customer']['age']);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>



    <script>
        $(document).ready(function() {
            let count = 0;

            // search item name suggestion (get item name from db)
            function initializeTypeahead(count) {
                $('#productname-' + count).typeahead({
                    source: function(query, process) {

                        return $.ajax({
                            url: "{{ route('autocomplete-part-code-invoice') }}",
                            method: 'POST',
                            data: {
                                query: query,
                            },
                            dataType: 'json',
                            success: function(data) {
                                console.log(data);
                                process(data);
                            }
                        });
                    }
                });
            }

            function initializeTypeaheads() {
                for (let i = 0; i <= count; i++) {
                    initializeTypeahead(i);
                }
            }

            function updateItemName(item_name, row, cuz_name) {
                let itemNameInput = row.find('.buy_price');
                let partDesc = row.find('.description');
                let sale_price = row.find('.sale_price');
                let category = row.find('.category');
                let warehouse = row.find('.warehouse');

                $.ajax({
                    type: 'POST',
                    url: "{{ route('get-part-data-invoice') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        item_name: item_name,

                    },

                    success: function(data) {


                        category.val(data.item_unit);
                        itemNameInput.val(data.buy_price ?? data.service_buy_price);
                        partDesc.val(data.descriptions);
                        sale_price.val(data.sale_price ?? 0);
                        warehouse.val(data.warehouse_id);
                        if (parseFloat(data.reorder_level_stock) >= parseFloat(data.quantity)) {
                            alert(data.quantity + " quantity!");
                        }

                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            }

            $("#addproduct").click(function(e) {
                e.preventDefault();
                count++;
                $.ajax({

                    type: 'GET',
                    url: "{{ route('get.part.data-unit') }}",
                    data: {
                        // _token: "{{ csrf_token() }}",

                    },
                    success: function(data) {
                        // itemNameInput.val(data.retail_price);
                        // partDesc.val(data.descriptions);
                        // exp_date.val(data.expired_date);

                        var selectBox = document.getElementById("unit-" + count);

                        // Loop through the data array
                        data.forEach(function(item) {
                            // Create an option element
                            var option = document.createElement("option");

                            // Set the value attribute to the unit id
                            option.value = item.unit;

                            // Set the text of the option to the unit name
                            option.text = item.unit;

                            // Append the option to the select element
                            selectBox.appendChild(option);
                        });


                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
                let rowCount = $("#showitem123 tr").length;
                let newRow = '<tr>' +

                    '<td class="text-center">' + (rowCount + 1) + '</td>' +
                    '<td style="display:none"><input type="hidden" class="form-control barcode typeahead" name="barcode[]" id="barcode-' +
                    count + '" autocomplete="off"></td>' +
                    '<td><input type="text" class="form-control productname typeahead" name="part_number[]" id="productname-' +
                    count + '" autocomplete="off"></td>' +
                    '<td><input type="text" class="form-control description typeahead" name="part_description[]"  id="description-' +
                    count + '" autocomplete="off"></td>' +
                    '<td><input type="text" class="form-control req amnt" name="product_qty[]" id="amount-' +
                    count +
                    '"   autocomplete="off" value="1"></td>' +
                    '<td><input type="text" name="category[]" id="category-' + count +
                    '"class="form-control category" ></td>' +
                    '<td><input type="text" class="form-control buy_price" name="buy_price[]" id="buy_price-' +
                    count + '" autocomplete="off " value="0"></td>' +
                    // '<td><input type="text" class="form-control exp_date " name="exp_date[]" id="exp_date-' +
                    // count +
                    // '"   autocomplete="off"></td>' +
                    '<td style="display : none;"><input type="text" class="form-control warehouse " name="warehouse[]" id="warehouse-' +
                    count +
                    '"   autocomplete="off"></td>' +

                    '<td style="text-align:center"><span class="currenty"></span><strong><span class="ttlText1" id="result-' +
                    count + '">0</span></strong></td>' +
                    '<td style="display:none"kI><input type="hidden" class="form-control sale_price" name="sale_price[]" value="0" id="sale_price-' +
                    count + '"></td>' +
                    '<input type="hidden" name="total_tax[]" id="taxa-' + count + '" value="0">' +

                    '<input type="hidden" class="ttInput" name="product_subtotal[]" id="total-' +
                    count + '" value="0">' +
                    '<input type="hidden" class="pdIn" name="product_id[]" id="pid-0" value="0">' +
                    // '<input type="hidden" attr-org="" name="unit[]" id="unit-0" value="">' +
                    '<input type="hidden" name="unit_m[]" id="unit_m-0" value="1">' +
                    '<input type="hidden" name="code[]" id="hsn-0" value="">' +
                    '<input type="hidden" name="serial[]" id="serial-0" value="">' +
                    '<td><button type="submit" class="btn btn-danger remove_item_btn" id="removebutton">Remove</button></td>' +
                    '</tr>';
                $("#showitem123").append(newRow);
                initializeTypeahead(count);
            });

            $(document).on('click', '.remove_item_btn', function(e) {
                e.preventDefault();
                let row_item = $(this).parent().parent();
                $(row_item).remove();

                // Update row numbers
                $('#showitem123 tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });

                initializeTypeaheads();
            });

            $(document).on('change', '.productname', function() {
                let itemCode = $(this).val();
                let row = $(this).closest('tr');
                let cuz_name = $("#type").val();
                updateItemName(itemCode, row, cuz_name);
            });

            // Initialize typeahead for the first row
            initializeTypeahead(count);
            $(document).on("click", '#calculate', function(e) {
                e.preventDefault();
                let total = 0;
                // let totalTax = 0;

                for (let i = 0; i < (count + 1); i++) {
                    let salePriceCategory = $('#category-' + i).val();
                    console.log(salePriceCategory);
                    var qty = parseInt($('#amount-' + i).val() || 0);
                    // var item_name = $('#productname-' + i).val() || 0;
                    // var sel = $('#focsel-' + i).val() || 0;
                    let price;
                    if (salePriceCategory === 'Lab') {
                        // let cuz_name = $("#type").val();
                        // console.log(cuz_name);
                        // price = cuz_name === "Whole Sale" ? price = $('#price-' + i).val() :
                        //     $('#retail_price-' + i).val() || 0;
                        // console.log($('#retail_price-' + i).val() || 0);
                        // $("#price-" + i + ).val(priceValue);
                        // b_price = parseInt($('#sale_price-' + i).val() || 0);
                        // price = parseInt($('#buy_price-' + i).val() || 0) - b_price;
                        price = parseInt($('#buy_price-' + i).val() || 0);

                        console.log(price);
                    } else {
                        price = parseInt($('#buy_price-' + i).val() || 0);
                        console.log(price);
                    }
                    // } else if (salePriceCategory === 'Retail') {
                    //     price = parseInt($('#retail_price-' + i).val() || 0);
                    // }
                    // let taxRate = parseFloat($('#vat-' + i).val() || 0);
                    // if (!isNaN(taxRate) && taxRate >= 0) {
                    //     let itemTax = (price * qty * taxRate) / 100;
                    //     totalTax += itemTax;
                    // }
                    $("#result-" + i).text(price * qty);
                    // if (!isNaN(taxRate) && taxRate > 0) {
                    //     let discount = (price * qty * taxRate) / 100;
                    //     $("#result-" + i).text((price * qty) - discount);
                    // } else {
                    //     $("#result-" + i).text(price * qty);
                    // }

                    total += price * qty;
                }
                // let taxt = total * 0.05; // Calculate tax based on the updated total
                // taxt = Math.ceil(taxt);
                // let total_total = total - totalTax;
                $("#invoiceyoghtml").val(total);
                // $("#commercial_text").val(totalTax); // Update tax value
                // $("#total").val(total_total);
                $('#total_total').val(total);
                $('#total_discount').val('');
            });


        });
    </script>
    <script>
        $(document).on('click', '.remove_item_btn', function(e) {
            e.preventDefault();
            let row_item = $(this).parent().parent();
            $(row_item).remove();
            count--;
        });


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        function paidFunction() {
            let paid = document.getElementById("paid").value;
            let total_p = document.getElementById("total_total").value;
            let balance = total_p - paid;
            $("#balance").val(balance); //update balance
        }
    </script>


    {{-- <script>
        $(document).ready(function() {
            var path = "{{ route('customer_service_search') }}";


            $('#customer').typeahead({
                source: function(query, process) {
                    return $.get(path, {
                        query: query
                    }, function(data) {
                        // Format the data for Typeahead
                        var formattedData = [];
                        $.each(data, function(index, customer) {
                            // Check if the query matches the name or phone number
                            if (customer.name.toLowerCase().indexOf(query
                                    .toLowerCase()) !== -1) {
                                // If the query matches the name, show the name
                                formattedData.push(customer.name);
                            } else if (customer.phno.indexOf(query) !== -1) {
                                // If the query matches the phone number, show the phone number
                                formattedData.push(customer.phno);
                            }
                        });
                        return process(formattedData);
                    });
                }
            });

            $('#customerPhone').on('keyup', function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    let customerPhone = $(this).val();

                    // Make AJAX request with customer phone number
                    // Similar to the AJAX request in the customerName keyup event
                }
            });

        });
    </script> --}}



    <script>
        $("input").on("change", function() {
            if (this.value && moment(this.value, "YYYY-MM-DD").isValid()) {
                this.setAttribute(
                    "data-date",
                    moment(this.value, "YYYY-MM-DD").format("DD/MM/YYYY")
                );
            } else {
                this.setAttribute("data-date", "dd/mm/yyyy");
            }
        }).trigger("change");
    </script>
    <script>
        //Enter Key click add row
        $(document).on('keydown', '.form-control', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#addproduct').click();
            }
        });
    </script>
    <script>
        $(document).on("input", "#total_discount", function() {
            let subtotal = parseFloat($("#invoiceyoghtml").val()) || 0;
            let discount = parseFloat($(this).val()) || 0;
            let total = subtotal - discount;
            $("#total_total").val(total);
        });

        $(document).on("input", "#paid", function() {
            let paid = document.getElementById("paid").value;
            let total_p = document.getElementById("total_total").value;
            let balance = total_p - paid;
            $("#balance").val(balance); //update balance
        });
    </script>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const locationSelect = document.getElementById('location');
            const doctorSelect = document.getElementById('doctor_id');

            locationSelect.addEventListener('change', function() {
                const selectedBranch = locationSelect.value;

                // Clear the doctor select options
                doctorSelect.innerHTML = '<option value="">Select Doctor</option>';

                // Filter and add the doctors based on the selected branch
                @foreach ($doctors as $doctor)
                    if (selectedBranch === '{{ $doctor->branch }}') {
                        const option = document.createElement('option');
                        option.value = '{{ $doctor->id }}';
                        option.textContent = '{{ $doctor->name }}';
                        doctorSelect.appendChild(option);
                    }
                @endforeach
            });
        });
    </script> --}}
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const locationSelect = document.getElementById('location');
            const branchSelect = document.getElementById('branch');

            locationSelect.addEventListener('change', function() {
                branchSelect.value = locationSelect.value;
            });

            branchSelect.addEventListener('change', function() {
                locationSelect.value = branchSelect.value;
            });
        });
    </script> --}}

    {{-- //get doctor --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const locationSelect = document.getElementById('location') || document.getElementById(
                'location_user');
            const doctorSelect = document.getElementById('doctor_id');

            locationSelect.addEventListener('change', function() {
                const locationId = this.value;

                if (locationId) {
                    // Fetch doctors for the selected location
                    fetch(`/get-doctors?location=${locationId}`)
                        .then(response => response.json())
                        .then(data => {
                            doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
                            data.forEach(doctor => {
                                const option = document.createElement('option');
                                option.value = doctor.id;
                                option.textContent = doctor.name;
                                option.setAttribute('data-branch', doctor.branch);
                                doctorSelect.appendChild(option);
                            });
                        });
                } else {
                    // Clear doctors if no location selected
                    doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
                }
            });
        });
    </script>



    {{-- Invoice Number Update --}}

    <!-- <script>
        $('#location').on('change', function() {
            fetchAdminInvoiceUpdates(

            )

        });

        setInterval(() => {
            fetchAdminInvoiceUpdates();
            // console.log('interval');


        }, 9000);

        function fetchAdminInvoiceUpdates() {
            const location = document.getElementById("location").value;
            // console.log(branch);

            fetch(`{{ url('admin_invoice_no_updates') }}?branch=${location}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.json();
                })
                .then(data => {
                    const adminInvoiceNoInput = document.getElementById("invoice_no");
                    // console.log(data);
                    // console.log(adminInvoiceNoInput);

                    if (data.invoice_no && adminInvoiceNoInput.value !== data.invoice_no) {
                        adminInvoiceNoInput.value = data.invoice_no;

                        console.log(data);
                    }
                })
                .catch(error => {
                    console.error("Invoice update error:", error);
                    setTimeout(fetchAdminInvoiceUpdates, 5000);
                });
        }
    </script> -->

</body>

</HTML>
