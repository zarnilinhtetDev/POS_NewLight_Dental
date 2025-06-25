<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\Warehouse;

class ExpenseController extends Controller
{
    public function index()
    {
        $warehousePermission = auth()->user()->level
            ? json_decode(auth()->user()->level, true)
            : [];
        if (auth()->user()->is_admin == '1') {
            $expenses = Expense::latest()->get();
            $categories = ExpenseCategory::latest()->get();
            $branches = Warehouse::latest()->get();
        } else {
            $expenses = Expense::where('branch', $warehousePermission)->latest()->get();
            $categories = ExpenseCategory::latest()->get();
            $branches = Warehouse::latest()->get();
        }


        // dd($categories[0]);
        return view('expense.expense', [
            "expenses" => $expenses,
            "categories" => $categories,
            "branches" => $branches
        ]);
    }

    public function expenseStore(Request $request)
    {
        // // dd($request->all);
        // $timestamp = strtotime($request->date);
        // // Format the date in MM/DD/YYYY format
        // $formattedDate = date("d M Y", $timestamp);
        // $expense = new Expense();
        // $expense->name = $request->name;
        // $expense->category = $request->category;
        // $expense->amount = $request->amount;
        // $expense->date = $formattedDate;
        // $expense->description = $request->description;
        // $expense->save();
        Expense::create($request->all());
        return redirect(url('expense'))->with('success', 'Expense Created Successfully!');
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::latest()->get();
        $branches = Warehouse::latest()->get();
        return view('expense.expenseEdit', compact('expense', 'categories', 'branches'));
    }

    public function update(Request $request, Expense $expense)
    {
        // $timestamp = strtotime($request->date);
        // // Format the date in MM/DD/YYYY format
        // $formattedDate = date("d M Y", $timestamp);
        // // $expense = new Expense();
        // $expense->name = $request->name;
        // $expense->category = $request->category;
        // $expense->amount = $request->amount;
        // $expense->date = $formattedDate;
        // $expense->description = $request->description;
        // $expense->update();
        $expense->update($request->all());
        return redirect(url('expense'))->with('success', 'Expense Updated Successfully!');
    }

    public function delete(Expense $expense)
    {
        // $unit = Expense::find($id);
        $expense->delete();
        return redirect()->back()->with('delete', 'Expense Deleted Successfully!');
    }

    public function get_part_data_unit()
    {
        $units = Expense::all();
        return response()->json($units);
    }
}
