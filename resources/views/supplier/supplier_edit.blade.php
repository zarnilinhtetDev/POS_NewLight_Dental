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

                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Date -
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
                                <h1>Doctors Edit</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Doctors Edit
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
                                    <h3 class="card-title  " style="font-weight: bold;">Doctors Edit</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <form action="{{ url('doctors_update', $supplier->id) }}" method="POST">
                                        @csrf
                                        <div class="card-body">

                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control" id="name" required
                                                    autofocus name="name" value="{{ $supplier->name }}">
                                            </div>

                                            <div class="form-group">
                                                <label for="phno">Phone Number</label>
                                                <input type="text" class="form-control" id="phone number"
                                                    name="phno" value="{{ $supplier->phno }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="sale_commission">Sale Commission</label>
                                                <input type="number" class="form-control" id="sale_commission"
                                                    name="sale_commission" value="{{ $supplier->sale_commission }}"
                                                    required>
                                            </div>

                                            <div class="form-group">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control" id="phone number"
                                                    name="address" value="{{ $supplier->address }}" required>
                                            </div>

                                            {{-- @if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin')
                                                <div class="form-group">
                                                    <label for="branch">Location<span
                                                            class="text-danger">*</span></label>
                                                    <select name="branch" id="branch" class="form-control" required>
                                                        <option selected disabled>Select Location</option>
                                                        @foreach ($branches as $branch)
                                                            <option value="{{ $branch->id }}"
                                                                {{ $branch->id == $supplier->branch ? 'selected' : '' }}>
                                                                {{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <div class="form-group" style="display: none;">
                                                    <label for="branch">Location<span
                                                            class="text-danger">*</span></label>
                                                    <input class="form-control" type="text" name="branch"
                                                        id="branch" value="{{ auth()->user()->level }}">
                                                </div>
                                            @endif --}}

                                            <div class="form-group">
                                                <label for="branch">Location<span class="text-danger">*</span></label>
                                                <select name="branch" id="branch" class="form-control" required>
                                                    <option selected disabled>Select Location</option>
                                                    @if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin')
                                                        @foreach ($branches as $branch)
                                                            <option value="{{ $branch->id }}"
                                                                {{ $branch->id == $supplier->branch ? 'selected' : '' }}>
                                                                {{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        @foreach ($branches as $branch)
                                                            @php
                                                                $userPermis = auth()->user()->level
                                                                    ? json_decode(auth()->user()->level)
                                                                    : [];
                                                            @endphp
                                                            @if (in_array($branch->id, $userPermis))
                                                                <option value="{{ $branch->id }}"
                                                                    {{ $branch->id == $supplier->branch ? 'selected' : '' }}>
                                                                    {{ $branch->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>


                                            <div class="form-group">
                                                <label for="phno">Description</label>
                                                <textarea name="description" id="description" cols="10" rows="3" class="form-control">{{ $supplier->description }}</textarea>
                                            </div>

                                        </div>
                                        <div class="modal-footer justify-content-end">

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
