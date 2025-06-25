<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\Warehouse;

class ExpenseController extends Controller
{
    // public function index()
    // {
    //     $warehousePermission = auth()->user()->level
    //         ? json_decode(auth()->user()->level, true)
    //         : [];
    //     if (auth()->user()->is_admin == '1') {
    //         $expenses = Expense::latest()->get();
    //         $categories = ExpenseCategory::latest()->get();
    //         $branches = Warehouse::latest()->get();
    //     } else {
    //         $expenses = Expense::where('branch', $warehousePermission)->latest()->get();
    //         $categories = ExpenseCategory::latest()->get();
    //         $branches = Warehouse::latest()->get();
    //     }


    //     // dd($categories[0]);
    //     return view('expense.expense', [
    //         "expenses" => $expenses,
    //         "categories" => $categories,
    //         "branches" => $branches
    //     ]);
    // }

    public function index(Request $request)
    {
        $warehousePermission = auth()->user()->level
            ? json_decode(auth()->user()->level, true)
            : [];

        $selectedBranch = $request->input('branch'); // Get selected branch from request
        // dd($selectedBranch);

        if (auth()->user()->is_admin == '1') {
            $expenses = Expense::latest()->get();
            $branches = Warehouse::latest()->get();

            // Only fetch categories if a branch is selected
            if ($selectedBranch) {
                $categories = ExpenseCategory::where('branch', $selectedBranch)->latest()->get();
            } else {
                $categories = collect(); // empty collection
            }
        } else {
            $expenses = Expense::whereIn('branch', $warehousePermission)->latest()->get();
            $branches = Warehouse::whereIn('id', $warehousePermission)->latest()->get();

            // Only fetch categories if a branch is selected and within permitted branches
            if ($selectedBranch && in_array($selectedBranch, $warehousePermission)) {
                $categories = ExpenseCategory::where('branch', $selectedBranch)->latest()->get();
            } else {
                $categories = collect(); // empty collection
            }
        }

        return view('expense.expense', [
            "expenses" => $expenses,
            "categories" => $categories,
            "branches" => $branches,
            "selectedBranch" => $selectedBranch,
        ]);
    }


    public function getCategory(Request $request)
    {
        $location = $request->input('location');

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level, true) : [];

        if (auth()->user()->is_admin == '1') {
            $categories = ExpenseCategory::where('branch', $location)->get();
        } else {
            if (in_array($location, $warehousePermission)) {
                $categories = ExpenseCategory::where('branch', $location)->get();
            } else {
                $categories = [];
            }
        }

        return response()->json($categories);
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
