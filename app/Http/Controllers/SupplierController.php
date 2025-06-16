<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    //
    public function index()
    {
        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $suppliers = Supplier::latest()->get();
            $branches = Warehouse::latest()->get();
        } else {
            $suppliers = Supplier::where('branch', auth()->user()->level)->latest()->get();
            $branches = Warehouse::latest()->get();
        }

        return view('supplier.supplier', compact('suppliers', 'branches'));
    }
    // public function store(Request $request)
    // {
    //     try {
    //         $validate = $request->validate([
    //             'name' => 'required',
    //             'phno' => 'required',
    //             'address' => 'required',
    //             'sale_commission' => 'nullable',
    //             'description' => 'nullable',
    //         ]);

    //         $doctor = new Supplier();
    //         $doctor->name = $request->name;
    //         $doctor->phno = $request->phno;
    //         $doctor->address = $request->address;
    //         $doctor->sale_commission = $request->sale_commission ?? 30;
    //         $doctor->description = $request->description;
    //         $doctor->branch = $request->branch;
    //         $doctor->save();

    //         return redirect()->back()->with('success', 'Doctors Added Successfully!');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    //     }
    // }
    public function store(Request $request)
    {
        try {
            $validate = $request->validate([
                'name' => 'required',
                'phno' => 'required',
                'address' => 'required',
                'sale_commission' => 'nullable',
                'description' => 'nullable',
            ]);

            $existingDoctor = Supplier::where('name', $request->name)->where('branch', $request->branch)->first();

            if ($existingDoctor) {
                return redirect()->back()->with('error', 'Error: Doctor with the same name and branch already exists.');
            }

            $doctor = new Supplier();
            $doctor->name = $request->name;
            $doctor->phno = $request->phno;
            $doctor->address = $request->address;
            $doctor->sale_commission = $request->sale_commission ?? 30;
            $doctor->description = $request->description;
            $doctor->branch = $request->branch;
            $doctor->save();

            return redirect()->back()->with('success', 'Doctor Added Successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function edit(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        $branches = Warehouse::latest()->get();

        return view(
            'supplier.supplier_edit',
            compact('supplier', 'branches')
        );
    }
    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        $supplier->update($request->all());
        return redirect('doctors')->with('success', 'Doctors Updated Successful!');
    }
    public function delete($id)
    {
        $supplier = Supplier::find($id);
        $supplier->delete();
        return redirect('doctors')->with('success', 'Doctors Delete Successful!');
    }
}
