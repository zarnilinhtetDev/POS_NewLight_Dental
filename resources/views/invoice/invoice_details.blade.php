<!DOCTYPE html>
<HTML>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}" />

</head>
<style>
    @media print {
        body {
            color: black;
            /* Set text color for printing */
        }


    }

    @media print {

        #test,
        #printButton,
        .excelButton {
            display: none;
        }

        @page {
            size: auto;
            margin: 0;
        }
    }

    @media print {
        body {
            -webkit-print-color-adjust: exact;
        }
    }
</style>

<body style="margin:25px;">

    <div>
        <div class="row" style="margin-top: 10px;">
            <h4 class="text-center fw-bold">Invoice</h4>
            @foreach ($branchs as $branch)
                @if ($branch->id == $invoice->branch)
                    @if ($branch && Str::contains($branch->name, 'အောင်ပန်း'))
                        <p class="text-center fw-bold" style="font-size: 14px;">
                            <br>
                            အမှတ်၅၅(က) ၊နယ်မြေ(၁)၊ ချယ်ရီလမ်း၊ညောင်ပင်ထောင်ရပ်ကွက်။
                            <br>
                            (ပြည်ထောင်စုလမ်းမကြီးအနီး)
                            အောင်ပန်းမြို့။<br>
                            Phone : 09444701404 ,095195108
                        </p>
                    @elseif ($branch && Str::contains($branch->name, 'ကျိုင်းတုံ'))
                        <p class="text-center fw-bold" style="font-size: 14px;">
                            <br>
                            အမှတ် (၄၇)၊ နောင်တုံကန်ပတ်လမ်း၊ ကျိုင်းအင်းရပ်၊(ရပ်ကွက် ၅ ) ကျိုင်းတုံမြို့နယ်။
                            <br>
                            Phone : 09444701404 ,095151239
                        </p>
                    @elseif ($branch->name == 'New Light(တောင်ကြီး)')
                        <p class="text-center fw-bold" style="font-size: 14px;">
                            <br>
                            အမှတ် (၄၈) မြေညီထပ်၊
                            စဝ်စံထွန်းလမ်း နှင့် စာတိုက်အဆင်းလမ်း( သပြေလမ်း) ထောင့်
                            <br>
                            မြို့မရပ်ကွက် ၊ တောင်ကြီးမြို့ ။
                            <br>
                            Phone : 09444701404 , 095195108
                        </p>
                    @elseif ($branch && Str::contains($branch->name, 'Bago'))
                        <p class="text-center fw-bold" style="font-size: 14px;">
                            <br>
                            အမှတ် (၉) ပထမထပ် ( ပဲခူးစျေးကြီးရှေ့၊ငွေတောင်ကြီးတိုက်)
                            မင်းလမ်း၊
                            <br>
                            ပန်းလှိုင်ရပ်ကွက် ၊ပဲခူးမြို့။
                            <br>
                            Phone : 09444701404 ,095195108
                        </p>
                    @elseif ($branch && Str::contains($branch->name, 'SPT'))
                        <p class="text-center fw-bold" style="font-size: 14px;">
                            <br>
                            အမှတ်-၁၅၈(မြေညီ)၊ဘုရင့်နောင်လမ်းမကြီး၊ ထန်းခြောက်ပင်လမ်းဆုံ၊
                            <br>
                            (၅/၇)ရပ်ကွက် { ရိုးမဘဏ်ရှေ့} ၊ ရန်ကုန်မြို့။
                            <br>
                            Phone : 09408572244 ,095151239
                        </p>
                    @elseif ($branch && Str::contains($branch->name, 'HTY'))
                        <p class="text-center fw-bold" style="font-size: 14px;">
                            <br>
                            အမှတ်-၁၂၅၈ (ခ)၊ ပထမထပ်၊ ကျန်စစ်သားလမ်းမပေါ် ၊ (၁၆) ရပ်ကွက်၊
                            <br>
                            {လှိုင်သာယာ City Mart အနီး} ၊ ရန်ကုန်မြို့။
                            <br>
                            Phone : 09408572244 ,095151239
                        </p>
                    @elseif ($branch && Str::contains($branch->name, 'Grand Moe Pearl'))
                        <p class="text-center fw-bold" style="font-size: 14px;">
                            <br>
                            အမှတ် B 103 (မြေညီ) ၊ ပင်လုံလမ်း
                            <br>
                            စျေးပိုင်းရပ်ကွက် ၊ ဗိုလ်ချုပ်လမ်းမကြီးဘေး (မြိုမစျေးမြောက်ဘက်)
                            တောင်ကြီးမြို့။
                            <br>
                            Phone : 09252252302 ,09975427529
                        </p>
                    @elseif ($branch && Str::contains($branch->name, '13th Street'))
                        <p class="text-center fw-bold" style="font-size: 14px;">
                            <br>
                            အမှတ် -၁၉၇(ပထမထပ်)၊ ၁၃ လမ်း နဲ့ ဘုန်းကြီးလမ်းကြား၊
                            <br>
                            အနော်ရထာလမ်းမပေါ် ၊ လမ်းမတော်မြို့နယ်၊ ရန်ကုန်မြို့။
                            <br>
                            Phone : 09762710777
                        </p>
                    @elseif ($branch && Str::contains($branch->name, 'Myaynigone'))
                        <p class="text-center fw-bold" style="font-size: 14px;">
                            <br>
                            တိုက်အမှတ် ၁၆၇ ၊ ပထမထပ်၊ ကျွန်းတောလမ်းနဲ့ ဗားဂရာလမ်းထောင့် ( Spirits မျက်စောင်းထိုး) ၊

                            <br>
                            စမ်းချောင်းမြို့နယ် ၊ရန်ကုန်မြို့။
                            <br>
                            Phone : 095151239
                        </p>
                    @elseif ($branch && Str::contains($branch->name, '39th street'))
                        <p class="text-center fw-bold" style="font-size: 14px;">
                            <br>
                            တိုက်အမှတ် (၈၂) ၊ ပထမထပ် (၃၉) လမ်း နဲ့ အနော်ရထာလမ်းထောင့် ( Lotteria
                            ကြက်ကြော်ဆိုင်မျက်စောင်းထိုး, ABC စတိုးဆိုင်အပေါ်ထပ်)

                            <br>
                            ကျောက်တံတားမြို့နယ်၊ ရန်ကုန်မြို့။
                            <br>
                            Phone : 095151239
                        </p>
                    @elseif ($branch && Str::contains($branch->name, 'Tachileik'))
                        <p class="text-center fw-bold" style="font-size: 14px;">
                            <br>
                            အမှတ် (661 )၊ပါလျှံ (2)ရပ်ကွက်၊ (ရန်အောင်မြေရပ်ကွပ်ရုံးဘေး)၊ အာခါစျေးလမ်းသွယ်
                            <br>
                            တာချီလိတ်မြို့။
                            <br>
                            Phone : 09444701404
                        </p>
                    @endif
                @endif
            @endforeach

        </div>
        {{-- <h1>
            Invoice
        </h1> --}}

        <div class="content-wrapper">
            <div class="content-body">
                <div class="">
                    <div class="card-content">

                        <div class="card-body">

                            <div class="row" style="display:flex;position:relative">

                                <div style="width:40%;">
                                    <h4>Invoice to</h4>
                                    <div class="input-group"><span style="font-weight:bolder"> Name :&nbsp;
                                        </span>{{ $invoice->customer_name }} </div>

                                    <div class="input-group"><span style="font-weight:bolder"> Age:&nbsp;
                                        </span>{{ $invoice->age }} </div>
                                    <div class="input-group"><span style="font-weight:bolder"> Phone Number :&nbsp;
                                        </span>{{ $invoice->phno }} </div>
                                    <div class="input-group"><span style="font-weight:bolder"> Address :&nbsp;
                                        </span>{{ $invoice->address }}

                                    </div>

                                    <br>

                                </div>


                                <div style="width:30%;position:absolute;right:0px;top:0px;" class="mt-4">
                                    <div class="input-group"><span style="font-weight:bolder"> Doctor Name :&nbsp;
                                        </span> {{ $invoice->doctor->name }}

                                    </div>

                                    <div class="input-group"><span style="font-weight:bolder"> Invoice Number :&nbsp;
                                        </span> {{ $invoice->invoice_no }}

                                    </div>
                                    <div class="input-group"><span style="font-weight:bolder"> Invoice Date :&nbsp;
                                        </span>{{ $invoice->invoice_date }}

                                    </div>
                                    <div class="input-group"><span style="font-weight:bolder">Doctor Name :&nbsp;
                                        </span>{{ $invoice->doctor->name }}

                                    </div>

                                </div>

                            </div>
                            <br>


                            <div class="row" style="margin-top: 1vh;">
                                <div class="table-responsive">
                                    <table class="table text-center table-bordered" style="">
                                        <thead class="bg-primary" style="color: black;">
                                            <tr class="text-white">
                                                <th>{{ trans('No') }}</th>
                                                <th>{{ trans('Treatment Name') }}</th>

                                                <th>{{ trans('Description') }}</th>
                                                <th>{{ trans('Qty') }}</th>
                                                <th>{{ trans('Category') }}</th>
                                                <th style="width: 10%">{{ trans('Price') }}</th>
                                                <th style="width: 10%">{{ trans('Total') }}

                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 1;
                                            @endphp
                                            @foreach ($sells as $sell)
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>{{ $sell->part_number }}</td>
                                                    <td>{{ $sell->description }}</td>
                                                    <td>{{ $sell->product_qty }}</td>

                                                    <td>{{ $sell->category }}</td>
                                                    <td> {{ $sell->buy_price ?? $sell->service_buy_price }}</td>

                                                    <td>
                                                        <span class="currenty"></span>
                                                        <span class='ttlText'>
                                                            {{ ($sell->buy_price ?? $sell->service_buy_price) * $sell->product_qty }}</span>
                                                    </td>
                                                </tr>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td style="font-weight: bolder;">Sub Total
                                                </td>
                                                <td style="font-weight: bolder;">
                                                    {{ number_format($invoice->sub_total) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td style="font-weight: bolder; ">Discount
                                                </td>
                                                <td style="font-weight: bolder;">
                                                    {{ number_format($invoice->discount_total) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td style="font-weight: bolder;">Total
                                                </td>
                                                <td style="font-weight: bolder;">
                                                    {{ number_format($invoice->total) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td style="font-weight: bolder;">Deposit
                                                </td>
                                                <td style="font-weight: bolder;">
                                                    {{ number_format($invoice->deposit) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td style="font-weight: bolder;"> Balance
                                                </td>
                                                <td style="font-weight: bolder;">
                                                    {{ number_format($invoice->remain_balance) }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>




                            <br><br>

                            <table width="60%" class="">
                                <tr>
                                    <td style="font-weight: bolder">Remark - {{ $invoice->remark }}
                                    </td>
                                </tr>
                            </table>


                        </div>

                    </div>
                </div>
            </div>
            <a onclick="printPage()" id="printButton" class="mt-4 btn btn-success">Print</a>

        </div>

    </div>


</body>

</HTML>
<script>
    function printPage() {
        window.print();
    }
</script>
