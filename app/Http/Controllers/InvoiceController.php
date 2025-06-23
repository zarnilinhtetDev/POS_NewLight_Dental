<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Sell;
use App\Models\Unit;
use App\Models\Invoice;
use Carbon\Traits\Date;
use App\Models\Customer;

use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\MakePayment;
use Illuminate\Http\Request;
use App\Models\InvoiceEditHistory;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    //
    // public function index()
    // {

    //     if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
    //         $warehouses = Warehouse::all();
    //         $invoices = Invoice::where('status', 'invoice')->latest()->get();
    //     } else {
    //         $warehouses = Warehouse::all();
    //         $invoices = Invoice::where('status', 'invoice')->where('branch', auth()->user()->level)->latest()->get();
    //     }
    //     return view('invoice.invoice_manage', compact(
    //         'invoices',
    //         'warehouses'
    //     ));
    // }

    public function index()
    {
        $warehousePermission = auth()->user()->level
            ? json_decode(auth()->user()->level, true)
            : [];

        if (auth()->user()->is_admin == '1') {
            $warehouses = Warehouse::all();
            $invoices = Invoice::where('status', 'invoice')->latest()->get();
        } else {
            // Ensure branch comparison is array-based
            $warehouses = Warehouse::whereIn('id', $warehousePermission)->get();
            $invoices = Invoice::where('status', 'invoice')
                ->whereIn('branch', $warehousePermission)
                ->latest()
                ->get();
        }

        return view('invoice.invoice_manage', compact('invoices', 'warehouses'));
    }






    public function customer_invoice($customer_id = null)
    {
        $query = Invoice::where('status', 'invoice');

        if (!auth()->user()->is_admin && auth()->user()->type !== 'Admin') {
            $query->where('branch', auth()->user()->level);
        }

        if ($customer_id) {
            $query->where('customer_id', $customer_id); // Make sure your invoices table has `customer_id`
        }

        $invoices = $query->latest()->get();
        $warehouses = Warehouse::all();

        return view('invoice.customer_invoice', compact('invoices', 'warehouses'));
    }


    public function quotation()
    {
        $quotations = Invoice::where('status', 'quotation')->latest()->get();
        return view('quotation.quotation_manage', compact(
            'quotations'
        ));
    }

    public function quotation_register()
    {
        $quotations = Invoice::whereNotNull('quote_no')->latest()->get();
        $quotation_no = "Quote-" . count($quotations) + 1;
        $units = Unit::all();
        $warehouses = Warehouse::all();
        return view('quotation.quotation', compact('quotation_no', 'units', 'warehouses'));
    }

    // public function invoice()
    // {
    //     $items = Item::latest()->get()->first();
    //     $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level, true) : [];
    //     if (auth()->user()->is_admin == '1') {
    //         $doctors = Supplier::latest()->get();
    //     } else {
    //         $doctors = Supplier::where('branch', $warehousePermission)->latest()->get();
    //     }
    //     $invoices = Invoice::where('status', 'invoice')->latest()->get();
    //     $invoice_no = "Invoice-" . count(Invoice::where('status', 'invoice')->withTrashed()->get()) + 1;
    //     $units = Unit::all();
    //     $warehouses = Warehouse::all();
    //     // dd($warehouses);
    //     return view('invoice.invoice', compact('invoice_no', 'units', 'warehouses', 'doctors', 'items'));
    // }

    public function invoice(Request $request)
    {
        $items = Item::latest()->first();
        $selectedBranch = $request->input('branch');
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level, true) : [];

        // Initialize doctors as empty collection
        $doctors = collect();

        if (auth()->user()->is_admin == '1') {
            // Admin: only show doctors if location is selected
            if ($selectedBranch) {
                $doctors = Supplier::where('branch', $selectedBranch)->latest()->get();
            }
        } else {
            // Non-admin: only show doctors if selected location is in their permissions
            if ($selectedBranch && in_array($selectedBranch, $warehousePermission)) {
                $doctors = Supplier::where('branch', $selectedBranch)->latest()->get();
            }
        }

        $invoices = Invoice::where('status', 'invoice')->latest()->get();
        // $invoice_no = "Invoice-" . (Invoice::where('status', 'invoice')->withTrashed()->count() + 1);
        $invoice_no = "Invoice-" . $selectedBranch .  (Invoice::where('status', 'invoice')->where('branch', $selectedBranch)->count() + 1);


        $units = Unit::all();
        $warehouses = Warehouse::all();

        return view('invoice.invoice', compact('invoice_no', 'units', 'warehouses', 'doctors', 'items', 'selectedBranch'));
    }

    public function getDoctors(Request $request)
    {
        $location = $request->input('location');
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level, true) : [];

        if (auth()->user()->is_admin == '1') {
            $doctors = Supplier::where('branch', $location)->get();
        } else {
            if (in_array($location, $warehousePermission)) {
                $doctors = Supplier::where('branch', $location)->get();
            } else {
                $doctors = [];
            }
        }

        return response()->json($doctors);
    }



    // public function pos_register()
    // {
    //     $invoices = Invoice::whereIn('status',  ['pos', 'suspend'])->latest()->get();
    //     $suspends = Invoice::where('status', 'suspend')->latest()->get();
    //     $invoice_no = "POS-" . count($invoices) + 1;
    //     $units = Unit::all();
    //     $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level, true) : [];
    //     if (auth()->user()->is_admin == '1') {
    //         $doctors = Supplier::latest()->get();
    //     } else {
    //         $doctors = Supplier::where('branch', $warehousePermission)->latest()->get();
    //     }
    //     $warehouses = Warehouse::all();
    //     return view('invoice.pos', compact('invoice_no', 'units', 'warehouses', 'suspends', 'doctors'));
    // }

    public function pos_register()
    {
        $invoices = Invoice::whereIn('status', ['pos', 'suspend'])->latest()->get();
        $suspends = Invoice::where('status', 'suspend')->latest()->get();
        $invoice_no = "POS-" . (count($invoices) + 1); // Make sure to use () for correct math

        $units = Unit::all();
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level, true) : [];

        if (auth()->user()->is_admin == '1') {
            $doctors = Supplier::latest()->get();
        } else {
            $doctors = Supplier::whereIn('branch', $warehousePermission)->latest()->get();
        }

        $warehouses = Warehouse::all();
        // dd($warehouses);

        return view('invoice.pos', compact('invoice_no', 'units', 'warehouses', 'suspends', 'doctors'));
    }







    //    public function pos()
    //     {
    //         if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
    //             $invoices = Invoice::where('status', 'pos')->latest()->get();
    //             return view('invoice.pos_manage', compact(
    //                 'invoices'
    //             ));
    //         } else {
    //             $invoices = Invoice::with(['sells' => function ($query) {
    //                 $query->where('warehouse', auth()->user()->level);
    //             }])
    //                 ->where('status', 'pos')
    //                 ->whereHas('sells', function ($query) {
    //                     $query->where('warehouse', auth()->user()->level);
    //                 })
    //                 ->latest()
    //                 ->get();
    //             return view('invoice.pos_manage', compact('invoices'));
    //         }
    //     }
    public function pos()
    {
        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $invoices = Invoice::where('status', 'pos')->latest()->get();
            return view('invoice.pos_manage', compact(
                'invoices'
            ));
        } else {
            $invoices = Invoice::where('status', 'pos')->where('sale_by', auth()->user()->name)->latest()->get();
            return view('invoice.pos_manage', compact(
                'invoices'
            ));
        }
    }

    public function invoice_register(Request $request)
    {
        // dd($request->all());

        // dd($request->input('sale_price'));
        $total_sale_price = 0;
        if ($request->input('sale_price')) {
            $total_sale_price = array_sum($request->input('sale_price'));
        }
        // dd($request->total - $total_sale_price);
        // if ($request->filled("doctor_id")) {
        //     return redirect()->back()->with("doctor_id", "You need to select Doctor!");
        // }

        $invoice_number = Invoice::where('invoice_no', $request->invoice_no)->get();
        $count = count($invoice_number);

        $doctor = Supplier::where('id', $request->doctor_id)->first();


        $doctor_commission = ($request->total - $total_sale_price) * ($doctor->sale_commission / 100);
        // dd((int)$doctor_commission);

        if ($count < 1) {
            $inv_number = $request->invoice_no;
        } else {
            $inv_number = $request->invoice_no;
            do {
                $inv_number++;
                $count = Invoice::where('invoice_no', $inv_number)->count();
            } while ($count > 0);
        }
        $count = count($request->part_description);
        $invoice = new Invoice();
        $invoice->doctor_commission = ceil((int)$doctor_commission);
        $invoice->customer_id = $request->customer_id;
        $invoice->customer_name = $request->name;
        $invoice->invoice_category = $request->quote_category;
        $invoice->doctor_id = $request->doctor_id;
        $invoice->phno  = $request->phno;
        $invoice->age = $request->age;
        $invoice->status  = $request->status;
        $invoice->sale_by  = auth()->user()->name;
        $invoice->location = $request->location;
        $invoice->branch = $request->branch;
        $invoice->type  = $request->type;
        $invoice->address  = $request->address;
        $invoice->invoice_no  = $inv_number;
        $invoice->invoice_date = $request->invoice_date;
        // $invoice->quote_date = $request->quote_date;
        $invoice->quote_no  = $request->quote_no;
        $invoice->overdue_date  = $request->overdue_date;
        $invoice->sub_total  = $request->sub_total;
        $invoice->total  = $request->total;
        $invoice->balance_due  = $request->balance_due;
        $invoice->discount_total  = $request->discount;
        $invoice->deposit  = $request->paid;
        $invoice->remain_balance  = $request->balance;
        $invoice->remark = $request->remark;

        $invoice->payment_method   = $request->payment_method;


        $invoice->save();
        $last_id = $invoice->id;
        for ($i = 0; $i < $count; $i++) {
            $result = new Sell();
            $result->invoiceid = $last_id;
            $result->customer_id = $request->customer_id;
            $result->description = $request->part_description[$i];
            $result->part_number = $request->part_number[$i];
            $result->product_qty = $request->product_qty[$i];
            $result->sale_price = $request->sale_price[$i];
            $result->buy_price = $request->buy_price[$i];
            $result->category = $request->category[$i];
            $result->warehouse = $request->warehouse[$i];
            $result->save();
        }

        if ($request->status == 'quotation') {
            return redirect('/quotation')->with('success', 'Quotation Added Successful!');
        } elseif ($request->status == 'invoice') {
            foreach ($invoice->sells as $sell) {
                $item = Item::where('item_name', $sell->part_number)->first();
                if ($item) {
                    $item->quantity -= $sell->product_qty;
                    $item->save();
                } else {
                    continue;
                }
            }
            return redirect('/invoice')->with('success', 'Invoice Added Successful!');
        } elseif ($invoice->status === 'pos') {

            foreach ($invoice->sells as $sell) {
                $item = Item::where('item_name', $sell->part_number)->first();
                if ($item) {
                    $item->quantity -= $sell->product_qty;
                    $item->save();
                } else {
                    continue;
                }
            }
            return redirect()->route('invoice_detail', ['invoice' => $invoice->id])->with('success', 'POS Register Successfully');
        } else if ($invoice->status === 'suspend') {
            return redirect()->back()->with('success', 'Suspend Added Successful!');
        } else {
            foreach ($invoice->sells as $sell) {
                $item = Item::where('item_name', $sell->part_number)->first();
                if ($item) {
                    $item->quantity -= $sell->product_qty;
                    $item->save();
                } else {
                    continue;
                }
            }
            return redirect('/invoice')->with('success', 'POS Added Successful!');
        }
    }




    public function quotation_delete($id)
    {
        $quotation = Invoice::find($id);
        Sell::where('invoiceid', $id)->delete();
        $quotation->delete();
        return redirect('/quotation')->with('success', 'Quotation Deleted Successful!');
    }
    public function invoice_delete($id)
    {
        // Find the invoice by its ID
        $invoice = Invoice::find($id);

        // Check if the invoice exists
        if ($invoice) {
            // Delete related Sell records
            Sell::where('invoiceid', $id)->delete();

            // Delete the invoice
            $invoice->delete();

            // Determine the redirection based on the invoice status
            if ($invoice->status == 'pos') {
                return back()->with('success', 'POS Deleted Successful!');
            } else {
                return redirect('/invoice')->with('success', 'Invoice Deleted Successful!');
            }
        } else {
            // Handle the case where the invoice does not exist
            return redirect('/invoice')->with('error', 'Invoice Not Found!');
        }
    }

    public function quotation_edit($id)
    {
        $quotation = Invoice::find($id);
        $sell = Sell::where('invoiceid', $id)->get();
        $warehouses = Warehouse::all();

        return view('quotation.quotation_edit', compact('quotation', 'sell', 'warehouses'));
    }

    public function suspend_delete($id)
    {
        $suspend = Invoice::find($id);
        Sell::where('invoiceid', $id)->delete();
        $suspend->delete();
        return redirect()->back()->with('delete', 'Suspend Deleted Successful!');
    }

    // public function invoice_edit($id)
    // {

    //     $invoice = Invoice::find($id);
    //     $warehouses = Warehouse::all();


    //     if ($invoice->status === 'suspend') {
    //         $sells = Sell::where('invoiceid', $id)->get();
    //         $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level, true) : [];
    //         if (auth()->user()->is_admin == '1') {
    //             $doctors = Supplier::latest()->get();
    //         } else {
    //             $doctors = Supplier::where('branch', $warehousePermission)->latest()->get();
    //         }

    //         return view('invoice.pos_edit', compact('invoice', 'sells', 'doctors', 'warehouses'));
    //     } else {
    //         // $sell = Sell::where('invoiceid', $id)->first();
    //         $sells = Sell::where('invoiceid', $id)->get();
    //         $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level, true) : [];
    //         if (auth()->user()->is_admin == '1') {
    //             $doctors = Supplier::latest()->get();
    //         } else {
    //             $doctors = Supplier::where('branch', $warehousePermission)->latest()->get();
    //         }
    //         // dd($sell->invoiceid);
    //         return view('invoice.invoice_edit', compact('invoice', 'sells', 'doctors', 'warehouses'));
    //     }
    // }

    public function invoice_edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $warehouses = Warehouse::all();
        $selectedBranch = $invoice->branch;

        // Initialize empty array for all cases
        $warehousePermission = [];

        // Only decode permissions for non-admin users
        if (auth()->user()->is_admin != '1' && auth()->user()->level) {
            try {
                $decoded = json_decode(auth()->user()->level, true);
                $warehousePermission = is_array($decoded) ? $decoded : [];
            } catch (\Exception $e) {
                $warehousePermission = [];
            }
        }

        // Doctor selection logic
        $doctors = collect();

        if (auth()->user()->is_admin == '1') {
            // Admin can see all doctors or filter by selected branch
            $doctors = $selectedBranch
                ? Supplier::where('branch', $selectedBranch)->latest()->get()
                : Supplier::latest()->get();
        } else {
            // Non-admin users get filtered by permissions
            if (!empty($warehousePermission)) {
                $doctors = $selectedBranch && in_array($selectedBranch, $warehousePermission)
                    ? Supplier::where('branch', $selectedBranch)->latest()->get()
                    : Supplier::whereIn('branch', $warehousePermission)->latest()->get();
            }
        }

        $sells = Sell::where('invoiceid', $id)->get();

        return $invoice->status === 'suspend'
            ? view('invoice.pos_edit', compact('invoice', 'sells', 'doctors', 'warehouses', 'selectedBranch'))
            : view('invoice.invoice_edit', compact('invoice', 'sells', 'doctors', 'warehouses', 'selectedBranch'));
    }


    public function invoice_update(Request $request, $id)
    {
        // dd($request->all());

        $invoice = Invoice::find($id);

        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found');
        }

        $total_sale_price = 0;
        if ($request->input('sale_price')) {
            $total_sale_price = array_sum($request->input('sale_price'));
        }
        // dd($total_sale_price);

        $doctor = Supplier::where('id', $request->doctor_id)->first();
        $doctor_commission = ($request->total - $total_sale_price) * ($doctor->sale_commission / 100);
        // dd((int)$doctor_commission);

        $invoice->doctor_commission = ceil((int)$doctor_commission);
        $invoice->customer_id = $request->customer_id;
        $invoice->customer_name = $request->name;
        $invoice->invoice_category = $request->quote_category;
        $invoice->doctor_id = $request->doctor_id;
        $invoice->phno  = $request->phno;
        $invoice->age = $request->age;
        $invoice->status  = $request->status;
        $invoice->sale_by  = auth()->user()->name;
        $invoice->location = $request->location;
        $invoice->type  = $request->type;
        $invoice->address  = $request->address;
        $invoice->invoice_date = $request->invoice_date;
        $invoice->status = $request->status;
        $invoice->quote_no  = $request->quote_no;
        $invoice->overdue_date  = $request->overdue_date;
        $invoice->sub_total  = $request->sub_total;
        $invoice->total  = $request->total;
        $invoice->balance_due  = $request->balance_due;
        $invoice->discount_total  = $request->discount;
        $invoice->deposit  = $request->paid;
        $invoice->branch = $request->branch;
        $invoice->remain_balance  = $request->balance;
        $invoice->remark = $request->remark;

        $invoice->payment_method   = $request->payment_method;
        $invoice->save();

        //invoice edit history
        $invoice_history = new InvoiceEditHistory();
        $invoice_history->invoice_id = $id;
        $invoice_history->user_id = auth()->user()->id;
        $invoice_history->user_name = auth()->user()->name;
        $invoice_history->invoice_no = $request->invoice_no;
        $invoice_history->total_amount = $request->total;
        $invoice_history->remark = $request->remark;
        $invoice_history->created_at = Carbon::now();
        $invoice_history->save();

        $sellsData = [];
        if ($request->input('part_description')) {
            foreach ($request->input('part_description') as $key => $partDescription) {
                $sellsData[] = [
                    'description' => $partDescription,
                    'part_number' => $request->input('part_number')[$key],
                    'product_qty' => $request->input('product_qty')[$key],
                    'category' => $request->input('category')[$key],
                    'sale_price' => $request->input('sale_price')[$key],
                    'buy_price' => $request->input('buy_price')[$key],
                    'warehouse' => $request->input('warehouse')[$key],
                    'invoiceid' => $id,
                    // 'customer_id' =>$request->id
                ];
            }
        }

        if ($invoice->status === 'invoice') {

            $oldQuantities = [];
            foreach ($invoice->sells as $key => $sell) {

                $oldQuantities[$key] = $sell->product_qty;
                // info($key);
                // info($oldQuantities[$key]);
            }

            foreach ($request->input('part_number') as $key => $partNumber) {
                $item = Item::where('item_name', $partNumber)->first();

                if (!$item) {
                    continue;
                }

                $currentQuantity = $item->quantity;
                $newQuantity = $currentQuantity + ($oldQuantities[$key] ?? 0) - $request->input('product_qty')[$key];
                $item->quantity = $newQuantity;
                $item->save();
                // info($key);
                // info($request->product_qty[$key]);
            }
        }

        Sell::where('invoiceid', $id)->delete();
        Sell::insert($sellsData);

        if ($invoice->status === 'quotation') {
            return redirect('quotation')->with('success', 'Quotation Update Successful');
        } elseif ($invoice->status === 'invoice') {
            return redirect('invoice')->with('success', 'Invoice Update Successful');
        } elseif ($invoice->status === 'pos') {
            return redirect(url('invoice_detail', $invoice->id))->with('success', 'POS Update Successful');
        }
        return redirect()->back()->with('error', 'Failed to update invoice');
    }

    public function change_invoice($id)
    {

        $invoice = Invoice::where('status', 'invoice')->get();
        $invoices = Invoice::find($id);
        foreach ($invoices->sells as $sell) {
            $item = Item::where('item_name', $sell->part_number)->first();
            if ($item) {
                $item->quantity -= $sell->product_qty;
                $item->save();
            } else {
                continue;
            }
        }
        $invoice_no = count($invoice) + 1;
        $quotation = Invoice::find($id);

        $quotation->status = 'invoice';
        $quotation->invoice_no = $invoice_no;
        $quotation->invoice_date = Carbon::today()->format('Y-m-d');
        $quotation->update();

        return redirect('/invoice')->with('status', 'Change Invoice Successful!');
    }





//     public function admin_invoice_no_updates(Request $request)
// {
//     $branch = $request->input('branch'); // frontend ကနေ branch id ပို့လာမယ်

//     // ညှိထားတဲ့ branch အတွက်သာ နောက်ဆုံးနံပါတ်ရှာမယ်
//     $latestNumber = Invoice::where('status', 'invoice')
//         ->where('branch', $branch)
//         ->selectRaw("MAX(CAST(SUBSTRING_INDEX(invoice_no, '-', -1) AS UNSIGNED)) as max_invoice_no")
//         ->value('max_invoice_no');

//     // နောက်တစ်ခုအတွက်နံပါတ် တွက်မယ်
//     $nextNumber = $latestNumber ? $latestNumber + 1 : 1;

//     // Invoice နံပါတ် ပြုလုပ်မယ်
//     $invoice_no = "Invoice-" . $nextNumber;

//     return response()->json(['invoice_no' => $invoice_no]);
//     // return response()->json(['invoice_no' => $invoice_no,'nextNumber'=>$nextNumber]);
//     // return response()->json($invoice_no);
// }





    // Customer Name Serarch
    public function customer_service_search(Request $request)
    {
        $data = Customer::select('name', 'phno')
            ->where('branch', $request->location)
            ->where('name', 'LIKE', '%' . $request->get('query') . '%')
            ->get(); // Retrieve all matching records
        info($data);
        // info($request->location);
        info('Query: ' . $request->get('location'));


        return response()->json($data);
    }


    public function customer_service_search_fill(Request $request)
    {

        $product = Customer::where('name', $request->model)->orWhere('phno', $request->model)
            ->where('branch', $request->location)
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$product) {
            return response()->json(['error' => 'Customer not found'], 404);
        }
        $responseData = [
            'customer' => $product,

        ];

        return response()->json($responseData);
    }


    //Customer Phone Search

    public function customer_phone_search(Request $request)
    {
        $data = Customer::select('name', 'phno')
            ->where('branch', $request->location)
            ->where('phno', 'LIKE', '%' . $request->get('query') . '%')
            ->get(); // Retrieve all matching records

        info($request->location);
        return response()->json($data);
    }


    public function customer_phone_search_fill(Request $request)
    {

        $product = Customer::where('phno', $request->model)
            ->where('branch', $request->location)
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$product) {
            return response()->json(['error' => 'Customer not found'], 404);
        }
        $responseData = [
            'customer' => $product,

        ];

        return response()->json($responseData);
    }




    public function autocompletePartCodeInvoice(Request $request)
    {
        $query = $request->get('query');
        $location = $request->get('location');
        $items = Item::where('item_name', 'like', '%' . $query . '%')
            ->pluck('item_name');
        return response()->json($items);
    }
    public function getPartDataInvoice(Request $request)
    {
        $result = Item::where('item_name', $request->item_name)
            ->first();
        if (!$result) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return response()->json($result);
    }

    //POS Route
    public function autocompletePartCode(Request $request)
    {
        $query = $request->get('query');
        $location = $request->get('location');
        $items = Item::where('item_name', 'like', '%' . $query . '%')
            ->pluck('item_name');
        return response()->json($items);
    }
    //POS
    public function getPartData(Request $request)
    {

        $item = Item::where('item_name', $request->itemname)
            ->orderBy('created_at', 'desc')
            ->first();


        if (!$item) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $resdata = [
            'item' => $item,
        ];

        return response()->json($resdata);
    }

    // public function autocompleteBarCode(Request $request)
    // {
    //     $query = $request->get('query');

    //     $location = $request->get('location');

    //     $barcode = Item::where('barcode', 'like', '%' . $query . '%')->where('warehouse_id', $location)
    //         ->pluck('barcode');
    //     info($barcode);
    //     return response()->json($barcode);
    // }



    // public function getBarcodeData(Request $request)
    // {

    //     $item = Item::where('barcode', $request->barcode)->where('warehouse_id', $request->location)
    //         ->orderBy('created_at', 'desc')
    //         ->first();


    //     if (!$item) {
    //         return response()->json(['error' => 'Product not found'], 404);
    //     }
    //     $resdata = [
    //         'item' => $item,
    //     ];

    //     return response()->json($resdata);
    // }

    // public function invoice_detail(Invoice $invoice)
    public function invoice_detail(Invoice $invoice)

    {

        if ($invoice->status === 'pos') {
            $sells = Sell::where('invoiceid', $invoice->id)->get();
            $invoices = Invoice::where('id', $invoice->id)->get();
            $branchs = Warehouse::all();

            return view('invoice.pos_detail', [
                'invoice' => $invoice,
                'invoices' => $invoices,
                'sells' => $sells,
                'branchs' => $branchs
            ]);
        } else {
            $sells = Sell::where('invoiceid', $invoice->id)->get();
            $branchs = Warehouse::all();
            return view('invoice.invoice_details', [
                'invoice' => $invoice,
                'sells' => $sells,
                'branchs' => $branchs

            ]);
        }
    }
    public function daily_sales()
    {
        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $daily_pos = Invoice::whereDate('created_at', Carbon::today())->where('status', 'pos')->get();
        } else {
            $daily_pos = Invoice::whereDate('created_at', Carbon::today())->where('status', 'pos')->where('sale_by', auth()->user()->name)->get();
        }
        // $daily_pos = Invoice::whereDate('created_at', Carbon::today())->where('status', 'pos')->get();
        return view('invoice.daily_sales', compact('daily_pos'));
    }
    public function item_search(Request $request)
    {
        $data = Item::select('item_name')
            ->where('item_name', 'LIKE', '%' . $request->get('query') . '%')->where('parent_id', 0)
            ->pluck('item_name'); // Retrieve all matching records
        return response()->json($data);
    }
    public function item_data_search_fill(Request $request)
    {
        $product = Item::where('item_name', $request->model)
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$product) {
            return response()->json(['error' => 'Item not found'], 404);
        }
        $warehouse = Warehouse::find($product->warehouse_id);
        $responseData = [
            'item' => $product,
            'warehouse' => $warehouse,
        ];
        return response()->json($responseData);
    }

    public function quotation_detail($id)
    {
        $quotation = Invoice::find($id);
        $sells = Sell::where('invoiceid', $id)->get();
        return view('quotation.quotation_details', compact('quotation', 'sells'));
    }

    public function invoiceEditHistory()
    {
        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $invoice_histories = InvoiceEditHistory::latest()->get();
            return view('invoice.invoice_edit_history', compact('invoice_histories'));
        } else {
            $invoice_histories = InvoiceEditHistory::where('branch', auth()->user()->level)->latest()->get();
            return view('invoice.invoice_edit_history', compact('invoice_histories'));
        }
    }
    public function invoice_receipt(Invoice $invoice)
    {
        $sells = Sell::where('invoiceid', $invoice->id)->get();
        $invoices = Invoice::where('id', $invoice->id)->get();
        $branchs = Warehouse::all();
        return view('invoice.invoice_receipt', [
            'invoice' => $invoice,
            'invoices' => $invoices,
            'sells' => $sells,
            'branchs' => $branchs
        ]);
    }

    public function invoice_daily_sales()
    {

        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $daily_pos = Invoice::whereDate('created_at', Carbon::today())->where('status', 'invoice')->get();
        } else {
            $daily_pos = Invoice::whereDate('created_at', Carbon::today())->where('status', 'invoice')->where('sale_by', auth()->user()->name)->get();
        }
        return view('invoice.invoice_daily_sales', compact('daily_pos'));
    }
    // public function allInvoiceCommissionUpdate()
    // {
    //     $invoices = Invoice::where('status', 'invoice')->latest()->get();
    //     // dd($invoice);
    //     try {
    //         DB::beginTransaction();
    //         foreach ($invoices as $invoice) {
    //             // dd($commission);
    //             $doctor = Supplier::where('id', $invoice->doctor_id)->get()->first();
    //             // dd($doctor);
    //             $sells = Sell::where('invoiceid', $invoice->id)->latest()->get();
    //             // dd($sells);
    //             $sale_price_total = 0;
    //             foreach ($sells as $sell) {
    //                 $sale_price_total += $sell->sale_price;
    //             }
    //             // dd($invoice->total);
    //             $doctor_commission = (int) (($invoice->total - $sale_price_total) * ($doctor->sale_commission / 100));
    //             // dd($doctor_commission);
    //             $invoice->doctor_commission = $doctor_commission;
    //             // dd($invoice->doctor_commission);
    //             $invoice->update();
    //         }
    //         DB::commit();
    //         return redirect()->back();
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         // Handle the error appropriately, you can log it or show an error message to the user
    //         return redirect()->back()->with('error', 'There was an error updating the reservation: ' . $e->getMessage());
    //     }
    // }


    //Make Payment


    public function payment($id)
    {
        $make_payments = Invoice::whereIn('status', ['invoice', 'pos'])->where('id', $id)->first();
        $payments = MakePayment::where('invoice_id', $id)
            ->where('invoice_no', '!=', null)
            ->get();
        $payments_number = MakePayment::latest()->first();
        return view('invoice.make_payment', compact('make_payments', 'payments', 'payments_number'));
    }



    public function payment_store(Request $request, $id)
    {

        if ($request->remain_balance == '0') {
            return redirect()->back()->with('error', 'Remaining Balance is 0 , Nothing To Pay!');
        }

        $make_payments = new MakePayment();

        $invoice = Invoice::where('status', 'invoice')->where('id', $id)->first();

        $make_payments->amount = $request->amount;
        $make_payments->note = $request->note;
        $make_payments->invoice_no = $request->invoice_no;
        $make_payments->invoice_id = $invoice->id;
        $make_payments->location = $request->branch;
        $make_payments->payment_date = $request->payment_date;
        $make_payments->cash_back = $request->cash_back;
        // $invoice->cash_back += $request->cash_back;
        $make_payments->save();

        //end substract receivable deposit when make makepayment


        $invoice->deposit = $request->amount + $invoice->deposit;
        $invoice->remain_balance = $invoice->remain_balance - ($request->amount - $request->cash_back);
        $invoice->update();



        return redirect(url('invoice'))->with('success', 'Payment Added Successfull!');
    }





    public function payment_edit($id)
    {
        $make_payments = MakePayment::where('id', $id)->first();

        if (!$make_payments) {
            return redirect()->back()->with('error', 'Payment not found!');
        }

        $invoice = Invoice::where('id', $make_payments->invoice_id)->first();
        // dd($invoice);

        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found!');
        }

        return view('invoice.make_payment_edit', compact('make_payments', 'invoice'));
    }




    public function payment_update($id, Request $request)
    {
        $make_payments = MakePayment::where('id', $id)->first();

        if (!$make_payments) {
            return redirect()->back()->with('error', 'Payment not found!');
        }

        $old_amount = $make_payments->amount;
        $old_cash_back = $make_payments->cash_back;

        $invoice = Invoice::where('id', $make_payments->invoice_id)->first();

        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found!');
        }

        // Update payment info
        $make_payments->amount = $request->amount;
        $make_payments->note = $request->note;
        $make_payments->invoice_no = $request->invoice_no;
        $make_payments->location = $request->branch;
        $make_payments->payment_date = $request->payment_date;
        $make_payments->cash_back = $request->cash_back;
        $make_payments->save();

        // Adjust invoice values
        $invoice->deposit = $invoice->deposit - $old_amount + $request->amount;
        $invoice->remain_balance = $invoice->remain_balance + ($old_amount - $old_cash_back) - ($request->amount - $request->cash_back);
        $invoice->update();

        return redirect()->route('make_payment', $make_payments->invoice_id)->with('success', 'Payment Updated Successfully!');
    }

    public function voucherView(MakePayment $make_payment)
    {
        $invoice = Invoice::where('id', $make_payment->invoice_id)->orWhere('id', $make_payment->invoice_record)->first();
        // $payment_methods = InvoicePaymentMethod::where('invoice_id', $invoice->id)->get();
        return view('invoice.invoice_voucher', [
            'invoice' => $invoice,
            'make_payment' => $make_payment,
            // 'payment_methods' => $payment_methods,
        ]);
    }
}
