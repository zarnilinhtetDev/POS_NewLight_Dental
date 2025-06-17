<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Unit;
use App\Models\PO_sells;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\PoMakePayment;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $po = PurchaseOrder::latest()->get();
        return view('purchase_order.purchase_order_manage', compact('po'));
    }
    public function purchase_order_register()
    {
        $suppliers = Supplier::all();
        $po_number = PurchaseOrder::whereNotNull('quote_no')->latest()->get();
        $units = Unit::all();
        $po_no = 'PO-' . count($po_number) + 1;
        $warehouses = Warehouse::all();
        return view('purchase_order.purchase_order', compact('po_no', 'suppliers', 'units', 'warehouses'));
    }

    //Customer Fill
    public function po_search(Request $request)
    {
        $query = $request->get('query');

        $data = Supplier::select('name', 'phno')
            ->where('name', 'LIKE', '%' . $query . '%')
            ->orWhere('phno', 'LIKE', '%' . $query . '%')
            ->get();

        return response()->json($data);
    }

    public function po_search_fill(Request $request)
    {
        // $userBranchId = auth()->user()->branch_id;
        $supplier = Supplier::where('name', $request->model)
            ->orWhere('phno', $request->model)
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$supplier) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $responseData = [
            'product' => $supplier,

        ];

        return response()->json($responseData);
    }

    public function purchase_order_store(Request $request)
    {

        $count = count($request->part_description);
        $invoice = new PurchaseOrder();
        $invoice->supplier_id = $request->supplier_id;
        $invoice->supplier_name = $request->supplier_name;
        $invoice->invoice_category = $request->quote_category;
        $invoice->phno  = $request->phno;
        $invoice->status  = $request->status;
        $invoice->type  = $request->type;
        $invoice->address  = $request->address;
        $invoice->invoice_no  = $request->invoice_no;
        $invoice->overdue_date = $request->overdue_date;
        $invoice->po_date = $request->po_date;
        $invoice->quote_no  = $request->po_number;
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
            $result = new PO_sells();
            $result->invoiceid = $last_id;
            $result->supplier_id = $request->supplier_id;
            $result->description = $request->part_description[$i];
            $result->part_number = $request->part_number[$i];
            $result->unit = $request->item_unit[$i];
            $result->exp_date = $request->exp_date[$i];
            $result->product_qty = $request->product_qty[$i];
            $result->product_price = $request->product_price[$i];
            $result->warehouse = $request->warehouse[$i];
            $result->save();
        }



        foreach ($invoice->po_sells as $po_sell) {
            $item = Item::where('item_name', $po_sell->part_number)->first();
            if ($item) {
                $item->quantity += $po_sell->product_qty;
                $item->save();
            } else {
                continue;
            }
        }


        return redirect('/purchase_order_manage')->with('success', 'Purchase Order Added Successful!');
    }

    public function edit($id)
    {
        $suppliers = Supplier::all();
        $purchase_orders = PurchaseOrder::find($id);
        $purchase_sells = PO_sells::where('invoiceid', $id)->get();
        $warehouses = Warehouse::all();
        return view('purchase_order.purchase_order_edit', compact('purchase_orders', 'suppliers', 'purchase_sells', 'warehouses'));
    }

    public function purchase_order_update(Request $request, $id)
    {
        $count = count($request->part_description);
        $invoice = PurchaseOrder::find($id);
        $invoice->supplier_id = $request->supplier_id;
        $invoice->invoice_category = $request->quote_category;
        $invoice->phno  = $request->phno;
        $invoice->status  = $request->status;
        $invoice->type  = $request->type;
        $invoice->address  = $request->address;
        $invoice->invoice_no  = $request->invoice_no;
        $invoice->overdue_date = $request->overdue_date;
        $invoice->po_date = $request->po_date;
        $invoice->quote_no  = $request->po_number;
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


        $oldQuantities = [];
        foreach ($invoice->po_sells as $key => $po_sell) {
            $oldQuantities[$key] = $po_sell->product_qty;
        }
        foreach ($request->input('part_number') as $key => $partNumber) {
            $item = Item::where('item_name', $partNumber)->first();
            if (!$item) {
                continue;
            }
            $currentQuantity = $item->quantity;
            $newQuantity = $currentQuantity - ($oldQuantities[$key] ?? 0) + $request->input('product_qty')[$key];
            $item->quantity = $newQuantity;
            info($key);
            info($request->product_qty[$key]);
            $item->save();
        }

        PO_sells::where('invoiceid', $id)->delete();
        for ($i = 0; $i < $count; $i++) {
            $result = new PO_sells();
            $result->invoiceid = $last_id;
            $result->supplier_id = $request->supplier_id;
            $result->description = $request->part_description[$i];
            $result->part_number = $request->part_number[$i];
            $result->unit = $request->item_unit[$i];
            $result->product_qty = $request->product_qty[$i];
            $result->exp_date = $request->exp_date[$i];
            $result->product_price = $request->product_price[$i];
            $result->warehouse = $request->warehouse[$i];



            $result->save();
        }




        return redirect('/purchase_order_manage')->with('success', 'Purchase Order Updated Successful!');
    }

    public function po_delete($id)
    {

        DB::beginTransaction();

        try {

            PurchaseOrder::findOrFail($id)->delete();


            PO_sells::where('invoiceid', $id)->delete();


            DB::commit();

            return redirect('/purchase_order_manage')->with('success', 'Purchase Order Deleted Successfully!');
        } catch (\Exception $e) {

            DB::rollback();

            return redirect('/purchase_order_manage')->with('error', 'Failed to delete purchase order.');
        }

        // return redirect('/quotation')->with('success', 'Quotation Deleted Successful!');
    }

    public function details($id)
    {
        $purchase_order = PurchaseOrder::find($id);
        $purchase_sells = PO_sells::where('invoiceid', $id)->get();
        return view('purchase_order.purchase_order_details', compact('purchase_order', 'purchase_sells'));
    }


    // PO Payment

    public function po_payment($id)
    {
        $po_make_payments = PurchaseOrder::where('id', $id)->first();
        // dd($po_make_payments);
        $po_payments = PoMakePayment::where('po_id', $id)
            ->where('po_no', '!=', null)
            ->get();
        $payments_number = PoMakePayment::latest()->first();
        return view('purchase_order.po_payment', compact('po_make_payments', 'po_payments', 'payments_number'));
    }



    public function po_payment_store(Request $request, $id)
    {

        if ($request->remain_balance == '0') {
            return redirect()->back()->with('error', 'Remaining Balance is 0 , Nothing To Pay!');
        }

        $make_payments = new PoMakePayment();

        $po = PurchaseOrder::where('id', $id)->first();

        $make_payments->amount = $request->amount;
        $make_payments->note = $request->note;
        $make_payments->po_no = $request->po_no;
        $make_payments->po_id = $po->id;
        $make_payments->location = $request->branch;
        $make_payments->payment_date = $request->payment_date;
        $make_payments->cash_back = $request->cash_back;
        // $invoice->cash_back += $request->cash_back;
        $make_payments->save();

        //end substract receivable deposit when make makepayment


        $po->deposit = $request->amount + $po->deposit;
        $po->remain_balance = $po->remain_balance - ($request->amount - $request->cash_back);
        $po->update();



        return redirect(url('purchase_order_manage'))->with('success', 'Payment Added Successfull!');
    }





    public function po_payment_edit($id)
    {
        $make_payments = PoMakePayment::where('id', $id)->first();

        if (!$make_payments) {
            return redirect()->back()->with('error', 'Payment not found!');
        }

        $po = PurchaseOrder::where('id', $make_payments->po_id)->first();
        // dd($invoice);

        if (!$po) {
            return redirect()->back()->with('error', 'PO not found!');
        }

        return view('purchase_order.po_payment_edit', compact('make_payments', 'po'));
    }




    public function po_payment_update($id, Request $request)
    {
        $make_payments = PoMakePayment::where('id', $id)->first();

        if (!$make_payments) {
            return redirect()->back()->with('error', 'Payment not found!');
        }

        $old_amount = $make_payments->amount;
        $old_cash_back = $make_payments->cash_back;

        $po = PurchaseOrder::where('id', $make_payments->po_id)->first();

        if (!$po) {
            return redirect()->back()->with('error', 'Invoice not found!');
        }

        // Update payment info
        $make_payments->amount = $request->amount;
        $make_payments->note = $request->note;
        $make_payments->po_no = $request->po_no;
        $make_payments->location = $request->branch;
        $make_payments->payment_date = $request->payment_date;
        $make_payments->cash_back = $request->cash_back;
        $make_payments->save();

        // Adjust invoice values
        $po->deposit = $po->deposit - $old_amount + $request->amount;
        $po->remain_balance = $po->remain_balance + ($old_amount - $old_cash_back) - ($request->amount - $request->cash_back);
        $po->update();

        return redirect()->route('po_make_payment', $make_payments->po_id)->with('success', 'Payment Updated Successfully!');
    }


    public function PovoucherView(PoMakePayment $make_payment)
    {
        $po = PurchaseOrder::where('id', $make_payment->po_id)->orWhere('id', $make_payment->invoice_record)->first();
        // $payment_methods = InvoicePaymentMethod::where('invoice_id', $invoice->id)->get();
        return view('purchase_order.po_voucher_view', [
            'po' => $po,
            'make_payment' => $make_payment,
            // 'payment_methods' => $payment_methods,
        ]);
    }
}
