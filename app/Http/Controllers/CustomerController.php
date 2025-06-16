<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //index
    public function index()
    {
        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $customers = Customer::latest()->get();
            $branches = Warehouse::latest()->get();
        } else {
            $customers = Customer::where('branch', auth()->user()->level)->latest()->get();
            $branches = Warehouse::latest()->get();
        }
        return view('customer.customer', compact('customers', 'branches'));
    }

    public function credit($id)
    {
        $customer = Customer::find($id);
        $invoices = Invoice::where('customer_id', $id)->get();
        $total_amount = $invoices->sum('total');
        $balance = $invoices->sum('remain_balance');
        $deposit = $invoices->sum('deposit');
        return view('customer.credit', compact('invoices', 'customer', 'total_amount', 'balance', 'deposit'));
    }
    public function store(Request $request)
    {

        try {
            $validated = $request->validate(
                [
                    'name' => 'required',
                    'phno' => 'required',
                    'age' => 'required',
                    'address' => 'required',
                    'branch' => 'required',
                ]
            );
            Customer::create($validated);
            return redirect()->back()->with('success', 'New Patient Added Successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function edit(Request $request, $id)
    {
        $showCustomer = Customer::find($id);
        $branches = Warehouse::latest()->get();
        return view('customer.customer_edit', compact('showCustomer', 'branches'));
    }
    public function update($id, Request $request)
    {
        $customer = Customer::find($id);
        $customer->update($request->all());
        $customers = Customer::latest()->get();
        return redirect('patient')->with('success', 'Patient Updated Successful!');
    }
    public function delete($id)
    {
        $customer = Customer::find($id);
        $customer->delete();
        return redirect('patient')->with('success', 'Patient Deleted Successful!');
    }
}
