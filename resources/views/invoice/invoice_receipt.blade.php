<!DOCTYPE html>
<HTML>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            top: 3px;
            left: 3px;
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
            top: 3px;
            right: 0;
            color: black;
            opacity: 1;
        }
    </style>
    <style>
        @media print {
            body {
                color: black;
                /* Set text color for printing */
            }

            /* Add any other styles you want to modify for printing */
        }

        @media print {

            #test,
            #printButton,
            .excelButton {
                display: none;
            }

            #print1 {
                display: none;
            }

            #print2 {
                display: none;
            }

            #print3 {
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


</head>

<body>

    <div class="container mx-auto">
        <div class="row">
            <div class="col-6">
            </div>
            <div class="gap-2 pt-2 col-6 d-flex align-items-center justify-content-end">
                <a onclick="printPage()" id="printButton" class=" btn btn-success" class="btn btn-primary" style="border-radius:10px;">Print</a>
                <a href="{{ url('invoice_daily_sales') }}" class="btn btn-primary" style="border-radius:10px;" id="print2"><i class="fa-regular fa-calendar-days"></i> Daily Sales</a>
                <a id="test" href="{{ url('invoice') }}" class="btn btn-primary" style="border-radius:10px;">Back</a>
                {{-- <a href="{{ url('pos_register') }}" class="text-white btn btn-primary" style="border-radius:10px;"
                id="print3"><i class="fa-solid fa-circle-plus"></i> POS Register</a> --}}
            </div>
        </div>
        {{-- <div style="display: flex; justify-content: center; align-items: center;">
            <img src="{{ asset('img/cliniclogo.jpg') }}" alt="Clinic Logo" height="150px" style="width: 150px;">
    </div> --}}
    <div class="row">
        @foreach ($branchs as $branch)
        @if ($branch->id == $invoice->branch)
        @if ($branch && Str::contains($branch->name, 'အောင်ပန်း'))
        <p class="text-center fw-bold" style="font-size: 12px;">
            <br>
            အမှတ်၅၅(က) ၊နယ်မြေ(၁)၊ ချယ်ရီလမ်း၊ညောင်ပင်ထောင်ရပ်ကွက်။
            <br>
            (ပြည်ထောင်စုလမ်းမကြီးအနီး)
            အောင်ပန်းမြို့။<br>
            Phone : 09444701404 ,095195108
        </p>
        @elseif ($branch && Str::contains($branch->name, 'ကျိုင်းတုံ'))
        <p class="text-center fw-bold" style="font-size: 12px;">
            <br>
            အမှတ် (၄၇)၊ နောင်တုံကန်ပတ်လမ်း၊ ကျိုင်းအင်းရပ်၊(ရပ်ကွက် ၅ ) ကျိုင်းတုံမြို့နယ်။
            <br>
            Phone : 09444701404 ,095151239
        </p>
        @elseif ($branch->name == 'New Light(တောင်ကြီး)')
        <p class="text-center fw-bold" style="font-size: 12px;">
            <br>
            အမှတ် (၄၈) မြေညီထပ်၊
            စဝ်စံထွန်းလမ်း နှင့် စာတိုက်အဆင်းလမ်း( သပြေလမ်း) ထောင့်
            <br>
            မြို့မရပ်ကွက် ၊ တောင်ကြီးမြို့ ။
            <br>
            Phone : 09444701404 , 095195108
        </p>
        @elseif ($branch && Str::contains($branch->name, 'Bago'))
        <p class="text-center fw-bold" style="font-size: 12px;">
            <br>
            အမှတ် (၉) ပထမထပ် ( ပဲခူးစျေးကြီးရှေ့၊ငွေတောင်ကြီးတိုက်)
            မင်းလမ်း၊
            <br>
            ပန်းလှိုင်ရပ်ကွက် ၊ပဲခူးမြို့။
            <br>
            Phone : 09444701404 ,095195108
        </p>
        @elseif ($branch && Str::contains($branch->name, 'SPT'))
        <p class="text-center fw-bold" style="font-size: 12px;">
            <br>
            အမှတ်-၁၅၈(မြေညီ)၊ဘုရင့်နောင်လမ်းမကြီး၊ ထန်းခြောက်ပင်လမ်းဆုံ၊
            <br>
            (၅/၇)ရပ်ကွက် { ရိုးမဘဏ်ရှေ့} ၊ ရန်ကုန်မြို့။
            <br>
            Phone : 09408572244 ,095151239
        </p>
        @elseif ($branch && Str::contains($branch->name, 'HTY'))
        <p class="text-center fw-bold" style="font-size: 12px;">
            <br>
            အမှတ်-၁၂၅၈ (ခ)၊ ပထမထပ်၊ ကျန်စစ်သားလမ်းမပေါ် ၊ (၁၆) ရပ်ကွက်၊
            <br>
            {လှိုင်သာယာ City Mart အနီး} ၊ ရန်ကုန်မြို့။
            <br>
            Phone : 09408572244 ,095151239
        </p>
        @elseif ($branch && Str::contains($branch->name, 'Grand Moe Pearl'))
        <p class="text-center fw-bold" style="font-size: 12px;">
            <br>
            အမှတ် B 103 (မြေညီ) ၊ ပင်လုံလမ်း
            <br>
            စျေးပိုင်းရပ်ကွက် ၊ ဗိုလ်ချုပ်လမ်းမကြီးဘေး (မြိုမစျေးမြောက်ဘက်)
            တောင်ကြီးမြို့။
            <br>
            Phone : 09252252302 ,09975427529
        </p>
        @elseif ($branch && Str::contains($branch->name, '13th Street'))
        <p class="text-center fw-bold" style="font-size: 12px;">
            <br>
            အမှတ် -၁၉၇(ပထမထပ်)၊ ၁၃ လမ်း နဲ့ ဘုန်းကြီးလမ်းကြား၊
            <br>
            အနော်ရထာလမ်းမပေါ် ၊ လမ်းမတော်မြို့နယ်၊ ရန်ကုန်မြို့။
            <br>
            Phone : 09762710777
        </p>
        @elseif ($branch && Str::contains($branch->name, 'Myaynigone'))
        <p class="text-center fw-bold" style="font-size: 12px;">
            <br>
            တိုက်အမှတ် ၁၆၇ ၊ ပထမထပ်၊ ကျွန်းတောလမ်းနဲ့ ဗားဂရာလမ်းထောင့် ( Spirits မျက်စောင်းထိုး) ၊

            <br>
            စမ်းချောင်းမြို့နယ် ၊ရန်ကုန်မြို့။
            <br>
            Phone : 095151239
        </p>
        @elseif ($branch && Str::contains($branch->name, '39th street'))
        <p class="text-center fw-bold" style="font-size: 12px;">
            <br>
            တိုက်အမှတ် (၈၂) ၊ ပထမထပ် (၃၉) လမ်း နဲ့ အနော်ရထာလမ်းထောင့် ( Lotteria
            ကြက်ကြော်ဆိုင်မျက်စောင်းထိုး, ABC စတိုးဆိုင်အပေါ်ထပ်)

            <br>
            ကျောက်တံတားမြို့နယ်၊ ရန်ကုန်မြို့။
            <br>
            Phone : 095151239
        </p>
        @elseif ($branch && Str::contains($branch->name, 'Tachileik'))
        <p class="text-center fw-bold" style="font-size: 12px;">
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
    <div class=" row">
        <h6 class="text-center" style="font-size: 12px;">Sales Receipt<br>
            <?= $currentDate = date('d-m-Y') ?></h6>
    </div>

    <div class="row">
        <p class="" style="font-size: 12px;">Sale ID: {{ $invoice->invoice_no }}<br>Doctor
            Name : {{ $invoice->doctor->name }} <br>
            Patient Name - {{ $invoice->customer_name }}
            <br>Employee :
            {{ auth()->user()->name }}
        </p>
        <div class="mt-1 table-responsive">
            <table class="mt-1" style="font-size: 12px;width:100%">
                <thead>
                    <tr class="text-left">
                        <th style="width: 40%;font-size: 12px;">Item Name.</th>
                        <th style="width: 10%;font-size: 12px;">Qty</th>
                        <th style="width: 15%;font-size: 12px;">Price</th>
                        <th class="text-end" style="width: 10%;font-size: 12px;">Total</th>
                    </tr>
                </thead>
                <tbody class="text-center" style="height:30px">

                    @foreach ($invoices as $invoice)
                    @foreach ($invoice->sells as $key => $sell)
                    <tr class="text-start">
                        <td>{{ $sell->part_number }}</td>
                        <td>{{ $sell->product_qty }}</td>

                        <td>
                            {{ $sell->buy_price ?? $sell->service_buy_price }}
                            </d>
                        <td class="text-end">
                            {{ ($sell->buy_price ?? $sell->service_buy_price) * $sell->product_qty }}

                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
                <tfoot style="border-top: 2px solid black !important;font-size:12px">

                    <tr style="line-height: 20px;">
                        <td colspan="3" class="text-end fw-bold ">Total</td>
                        <td class="text-end fw-bold">{{ $invoice->total ?? 0 }}</td>
                    </tr>
                    <tr style="line-height: 20px;">
                        <td colspan="3" class="text-end fw-bold">Discount</td>
                        <td class="text-end fw-bold">{{ $invoice->discount_total ?? 0 }}</td>
                    </tr>
                    <tr style="line-height: 20px;">
                        <td colspan="3" class="text-end fw-bold">Deposit</td>
                        <td class="text-end fw-bold">{{ $invoice->deposit ?? 0 }}</td>
                    </tr>
                    <tr style="line-height: 30px;">
                        <td colspan="3" class="text-end fw-bold">Balance</td>
                        <td class="text-end fw-bold">{{ $invoice->remain_balance ?? 0 }}</td>
                    </tr>

                </tfoot>
            </table>
        </div>

    </div>
    </div>

    <script>
        function printPage() {
            window.print();
        }
    </script>


</body>

</HTML>