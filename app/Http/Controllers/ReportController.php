<?php

namespace App\Http\Controllers;

use App\Models\Sell;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function report_invoice($branch = null)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $doctors = Supplier::all();

        $invoicesQuery = Invoice::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', 'invoice');

        if ($branch) {
            $invoicesQuery->where('branch', $branch);
        }

        if (auth()->user()->is_admin == '1' || auth()->user()->level == 'Admin') {
            $invoices = $invoicesQuery->get();
        } else {
            $invoicesQuery->where('branch', auth()->user()->level);
            $invoices = $invoicesQuery->get();
        }

        $totalCash = (clone $invoicesQuery)->where('payment_method', 'Cash')->sum('total');
        $totalKbz = (clone $invoicesQuery)->where('payment_method', 'KBZ Pay')->sum('total');
        $totalCB = (clone $invoicesQuery)->where('payment_method', 'CB Pay')->sum('total');
        $totalOther = (clone $invoicesQuery)->where('payment_method', 'Others')->sum('total');

        $total = $invoices->sum('total');

        $branch_drop = Warehouse::all();
        $branchNames = $branch_drop->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All Invoices';

        return view('report.report_invoice', compact('invoices', 'total', 'doctors', 'totalCash', 'totalKbz', 'totalCB', 'totalOther', 'branch', 'branch_drop', 'currentBranchName'));
    }

    public function invoiceSearch(Request $request)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $startDate = $request->input('start_date', $startOfMonth);
        $endDate = $request->input('end_date', $endOfMonth);
        $branch = $request->input('branch');

        $doctors = Supplier::all();

        $invoicesQuery = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'invoice');

        if ($branch) {
            $invoicesQuery->where('branch', $branch);
        }

        if (auth()->user()->is_admin == '1' || auth()->user()->level == 'Admin') {
            $invoices = $invoicesQuery->get();
        } else {
            $invoicesQuery->where('branch', auth()->user()->level);
            $invoices = $invoicesQuery->get();
        }

        $totalCash = (clone $invoicesQuery)->where('payment_method', 'Cash')->sum('total');
        $totalKbz = (clone $invoicesQuery)->where('payment_method', 'KBZ Pay')->sum('total');
        $totalCB = (clone $invoicesQuery)->where('payment_method', 'CB Pay')->sum('total');
        $totalOther = (clone $invoicesQuery)->where('payment_method', 'Others')->sum('total');

        $total = $invoices->sum('total');

        $branch_drop = Warehouse::all();
        $branchNames = $branch_drop->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All Invoices';

        return view('report.report_invoice', compact('invoices', 'total', 'doctors', 'totalCash', 'totalKbz', 'totalCB', 'totalOther', 'branch', 'branch_drop', 'currentBranchName', 'startDate', 'endDate'));
    }

    public function doctor($branch = null)
    {
        if (auth()->user()->is_admin == '1' || auth()->user()->level == 'Admin') {
            $invoices = Invoice::all();
            $doctors = Supplier::whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->get();
            $doctorInvoices = [];
            foreach ($doctors as $doctor) {
                $doctorInvoices[$doctor->id] = Invoice::where('doctor_id', $doctor->id)->count();
            }
            $total = $invoices->sum('total');
        } else {
            $invoices = Invoice::where('branch', auth()->user()->level)->get();
            $doctors = Supplier::whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->where('branch', auth()->user()->level)->get();
            $doctorInvoices = [];
            foreach ($doctors as $doctor) {
                $doctorInvoices[$doctor->id] = Invoice::where('doctor_id', $doctor->id)
                    ->where('branch', auth()->user()->level)->count();
            }
            $total = $invoices->where('branch', auth()->user()->level)->sum('total');
        }

        $branch_drop = Warehouse::all();
        $branchNames = $branch_drop->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All Doctors';

        return view('report.doctor_report', compact('doctors', 'total', 'invoices', 'doctorInvoices', 'branchNames', 'currentBranchName', 'branch_drop'));
    }



    public function doctorSearch(Request $request)
    {
        $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');

        $invoices_search = [];
        $doctor_search = [];
        $doctorInvoices = [];
        $branch = $request->input('branch');

        if (auth()->user()->is_admin == '1' || auth()->user()->level == 'Admin') {
            $invoices_search = Invoice::whereDate('invoice_date', '>=', $start_date)
                ->whereDate('invoice_date', '<=', $end_date)
                ->when($branch, function ($query, $branch) {
                    return $query->where('branch', $branch);
                })
                ->get();

            $doctor_search = Supplier::whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->when($branch, function ($query, $branch) {
                    return $query->where('branch', $branch);
                })
                ->get();
        } else {
            $invoices_search = Invoice::whereDate('invoice_date', '>=', $start_date)
                ->whereDate('invoice_date', '<=', $end_date)
                ->where('branch', auth()->user()->level)
                ->when($branch, function ($query, $branch) {
                    return $query->where('branch', $branch);
                })
                ->get();

            $doctor_search = Supplier::whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->where('branch', auth()->user()->level)
                ->when($branch, function ($query, $branch) {
                    return $query->where('branch', $branch);
                })
                ->get();
        }

        foreach ($doctor_search as $doctor) {
            $doctorInvoices[$doctor->id] = Invoice::where('doctor_id', $doctor->id)
                ->whereDate('invoice_date', '>=', $start_date)
                ->whereDate('invoice_date', '<=', $end_date)
                ->count();
        }

        $search_total = $invoices_search->sum('total');
        $branch_drop = Warehouse::all();
        $branchNames = $branch_drop->pluck('name', 'id');
        $currentBranchName = $branch ? $branchNames[$branch] : 'All Doctors';
        return view('report.doctor_report', compact('doctor_search', 'search_total', 'invoices_search', 'doctorInvoices', 'branch_drop', 'branchNames', 'currentBranchName'));
    }



    public function doctorDetail($id)
    {
        $doctors = Supplier::findOrFail($id);
        $invoices = Invoice::where('doctor_id', $id)
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->get();
        $sells = Sell::all();
        $totalInvoices = Invoice::where('doctor_id', $id)->count();
        $invoice_qty = $invoices->count();
        $total = $invoices->sum('doctor_commission');

        return view('report.doctor_detail_report', compact('doctors', 'invoices', 'invoice_qty', 'total', 'totalInvoices', 'sells'));
    }

    public function doctorDetailSearch(Request $request, $id)
    {
        $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');

        $doctors = Supplier::findOrFail($id);
        $doctor_detail_search = Invoice::where('doctor_id', $id)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)->get();
        $sells = Sell::all();
        $search_total = $doctor_detail_search->sum('doctor_commission');
        // dd($search_total);
        $invoice_qty = $doctor_detail_search->count();
        $totalInvoices = Invoice::where('doctor_id', $id)->count();

        return view('report.doctor_detail_report', compact('doctors', 'doctor_detail_search', 'invoice_qty', 'search_total', 'totalInvoices', 'sells'));
    }


    public function reportExpense($branch = null)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $expensesQuery = Expense::whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        if ($branch) {
            $expensesQuery->where('branch', $branch);
        }

        if (auth()->user()->is_admin == '1' || auth()->user()->level == 'Admin') {
            $expenses = $expensesQuery->get();
        } else {
            $expensesQuery->where('branch', auth()->user()->level);
            $expenses = $expensesQuery->get();
        }

        $total = $expenses->sum('amount');

        $branch_drop = Warehouse::all();
        $branchNames = $branch_drop->pluck('name', 'id');
        $currentBranchName = $branch ? $branchNames[$branch] : 'All Expenses';

        return view('report.report_expense', compact('expenses', 'total', 'branch', 'branch_drop', 'currentBranchName'));
    }


    public function report_sale_return()
    {

        $invoices = Invoice::whereDate('created_at', today())->where('status', 'invoice')->where('balance_due', 'PO Return')->get();
        $total = $invoices->sum('total');

        return view('report.report_sale_return', compact('invoices', 'total'));
    }

    public function report_quotation()
    {

        $quotations = Invoice::whereDate('created_at', today())->where('status', 'quotation')->get();

        $total = $quotations->sum('total');

        return view('report.report_quotation', compact('quotations', 'total'));
    }

    public function report_po()
    {

        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {

            $pos = PurchaseOrder::whereDate('created_at', today())
                ->where('balance_due', 'PO')
                ->get();

            $total = $pos->sum('total');
        } elseif (auth()->user()->type == 'Warehouse' || auth()->user()->type == 'Shop') {

            $pos = PurchaseOrder::whereDate('created_at', today())
                ->where('balance_due', 'PO')
                ->get();

            $total = 0;
            foreach ($pos as $pos_item) {
                foreach ($pos_item->po_sells as $po_sell) {
                    if ($po_sell->warehouse === auth()->user()->level) {
                        $total += $pos_item->total;
                        break; // Exit the inner loop once a match is found
                    }
                }
            }
        }

        // $total = $pos->sum('total');
        return view('report.report_po', compact('pos', 'total'));
    }

    public function report_purchase_return()
    {

        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $pos = PurchaseOrder::whereDate('created_at', today())
                ->where('balance_due', 'Sale Return')
                ->get();

            $total = $pos->sum('total');
        } elseif (auth()->user()->type == 'Warehouse' || auth()->user()->type == 'Shop') {

            $pos = PurchaseOrder::whereDate('created_at', today())
                ->where('balance_due', 'Sale Return')
                ->get();

            $total = 0;
            foreach ($pos as $pos_item) {
                foreach ($pos_item->po_sells as $po_sell) {
                    if ($po_sell->warehouse === auth()->user()->level) {
                        $total += $pos_item->total;
                        break; // Exit the inner loop once a match is found
                    }
                }
            }
        }

        // $total = $pos->sum('total');

        return view('report.report_purchase_return', compact('pos', 'total'));
    }

    //Treatment Items Report

    public function report_item()
    {

        $items = Item::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->get();
        $total = $items->sum('buy_price') + $items->sum('service_buy_price');
        $total_2 = $items->sum('sale_price');
        // dd($total);

        return view('report.report_item', compact('items', 'total', 'total_2'));
    }

    public function itemSearch(Request $request)
    {

        $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');

        $search_items = Item::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->get();

        $search_total = $search_items->sum('buy_price') + $search_items->sum('service_buy_price');
        $search_total_2 = $search_items->sum('sale_price');

        return view('report.report_item', compact('search_items', 'search_total', 'search_total_2'));
    }


    // Clinic Items Report By Thu Zar


    public function report_clinic_item()
    {

        $items = Item::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])
            ->where('item_unit', 'Clinic')
            ->get();
        $total = $items->sum('buy_price') + $items->sum('service_buy_price');
        $total_2 = $items->sum('sale_price');
        // dd($total);

        return view('report.report_clinic_item', compact('items', 'total', 'total_2'));
    }


    public function ClinicItemSearch(Request $request)
    {

        $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');

        $search_items = Item::whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->where('item_unit', 'Clinic')
            ->get();


        $search_total = $search_items->sum('buy_price') + $search_items->sum('service_buy_price');
        $search_total_2 = $search_items->sum('sale_price');

        return view('report.report_clinic_item', compact('search_items', 'search_total', 'search_total_2'));
    }




    public function monthly_invoice_search(Request $request)
    {

        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_invoices = Invoice::where('status', 'invoice')
                ->where('balance_due', 'Invoice')
                ->whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->get();
            $search_total = $search_invoices->sum('total');
        } else {
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_invoices = Invoice::where('status', 'invoice')
                ->where('balance_due', 'Invoice')
                ->where('branch', auth()->user()->level)
                ->whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->get();
            $search_total = $search_invoices->where('branch', auth()->user()->level)->sum('total');
        }
        return view('report.report_invoice', compact('search_invoices', 'search_total'));
    }

    public function monthly_sale_return(Request $request)
    {
        $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
        $search_invoices = Invoice::where('status', 'invoice')
            ->where('balance_due', 'Po Return')
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->get();
        $search_total = $search_invoices->sum('total');

        return view('report.report_sale_return', compact('search_invoices', 'search_total'));
    }

    public function monthly_quotation_search(Request $request)
    {
        $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
        $search_quotations = Invoice::where('status', 'quotation')
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->get();
        $search_total = $search_quotations->sum('total');

        return view('report.report_quotation', compact('search_quotations', 'search_total'));
    }

    public function monthly_po_search(Request $request)
    {

        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_pos = PurchaseOrder::whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->where('balance_due', 'PO')
                ->get();
            $search_total = $search_pos->sum('total');
        } elseif (auth()->user()->type == 'Warehouse' || auth()->user()->type == 'Shop') {

            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_pos = PurchaseOrder::whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->where('balance_due', 'PO')
                ->get();

            $search_total = 0;
            foreach ($search_pos as $pos_item) {
                foreach ($pos_item->po_sells as $po_sell) {
                    if ($po_sell->warehouse === auth()->user()->level) {
                        $search_total += $pos_item->total;
                        break; // Exit the inner loop once a match is found
                    }
                }
            }
        }

        return view('report.report_po', compact('search_pos', 'search_total'));
    }

    public function monthly_purchase_return(Request $request)
    {

        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_pos = PurchaseOrder::whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->where('balance_due', 'Sale Return')
                ->get();
            $search_total = $search_pos->sum('total');
        } elseif (auth()->user()->type == 'Warehouse' || auth()->user()->type == 'Shop') {

            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_pos = PurchaseOrder::whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->where('balance_due', 'Sale Return')
                ->get();

            $search_total = 0;
            foreach ($search_pos as $pos_item) {
                foreach ($pos_item->po_sells as $po_sell) {
                    if ($po_sell->warehouse === auth()->user()->level) {
                        $search_total += $pos_item->total;
                        break; // Exit the inner loop once a match is found
                    }
                }
            }
        }

        return view('report.report_purchase_return', compact('search_pos', 'search_total'));
    }



    public function report_pos()
    {
        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $pos_data = Invoice::whereDate('created_at', today())->where('status', 'POS')->get();
            $today = Carbon::today();
            $sale_totals = DB::table('invoices')
                ->select('sale_by', DB::raw('count(*) as total_invoices'), DB::raw('sum(total) as sale_total'))
                ->whereDate('created_at', $today)
                ->where('status', 'POS')
                ->groupBy('sale_by')
                ->get();
            $total = $pos_data->sum('pos_data');
        } elseif (auth()->user()->type == 'Warehouse' || auth()->user()->type == 'Cashier') {
            $pos_data = Invoice::whereDate('created_at', today())->where('status', 'POS')->get();
            $total = 0;
            foreach ($pos_data as $pos_item) {
                foreach ($pos_item->po_sells as $po_sell) {
                    if ($po_sell->warehouse === auth()->user()->level) {
                        $total += $pos_item->total;
                        break; // Exit the inner loop once a match is found
                    }
                }
            }
        }
        return view('report.report_pos', compact('pos_data', 'total', 'sale_totals'));
    }
    public function monthly_pos_search(Request $request)
    {
        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_pos = Invoice::where('status', 'POS')
                ->whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->get();
            $search_total = $search_pos->sum('total');
            $sale_totals = DB::table('invoices')
                ->select('sale_by', DB::raw('count(*) as total_invoices'), DB::raw('sum(total) as sale_total'))
                ->whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->where('status', 'POS')
                ->groupBy('sale_by')
                ->get();
        } elseif (auth()->user()->type == 'Warehouse' || auth()->user()->type == 'Shop') {
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_pos = Invoice::where('status', 'POS')
                ->whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->get();
            $search_total = 0;
            foreach ($search_pos as $pos_item) {
                foreach ($pos_item->po_sells as $po_sell) {
                    if ($po_sell->warehouse === auth()->user()->level) {
                        $search_total += $pos_item->total;
                        break; // Exit the inner loop once a match is found
                    }
                }
            }
        }
        return view('report.report_pos', compact('search_pos', 'search_total', 'sale_totals'));
    }

    public function expenseSearch(Request $request)
    {
        $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
        $branch = $request->input('branch');

        $expensesQuery = Expense::whereBetween('date', [$start_date, $end_date]);

        if ($branch) {
            $expensesQuery->where('branch', $branch);
        }

        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $search_expenses = $expensesQuery->get();
        } else {
            $expensesQuery->where('branch', auth()->user()->level);
            $search_expenses = $expensesQuery->get();
        }

        $search_total = $search_expenses->sum('amount');

        $branch_drop = Warehouse::all();
        $branchNames = $branch_drop->pluck('name', 'id');
        $currentBranchName = $branch ? $branchNames[$branch] : 'All Expenses';

        return view('report.report_expense', compact('search_expenses', 'search_total', 'branch', 'branch_drop', 'currentBranchName', 'start_date', 'end_date'));
    }

    public function profit(Request $request)
    {

        $branch = $request->get('branch'); // Get branch ID from request

        $invoicesSubquery = DB::table('invoices')
            ->select(
                DB::raw('DATE_FORMAT(invoices.created_at, "%Y-%m") as month'),
                DB::raw('COUNT(invoices.id) as qty'),
                DB::raw('SUM(invoices.total) as total_income'),
                DB::raw('SUM(invoices.doctor_commission) as commission')
            )
            ->where('invoices.status', 'invoice')
            ->where(function ($query) use ($branch) {
                if ($branch) {
                    $query->where('invoices.branch', $branch);
                }
            })
            ->whereNull('invoices.deleted_at')
            ->groupBy(DB::raw('DATE_FORMAT(invoices.created_at, "%Y-%m")'));

        $expensesSubquery = DB::table('expenses')
            ->select(
                DB::raw('DATE_FORMAT(expenses.date, "%Y-%m") as month'),
                DB::raw('SUM(expenses.amount) as total_expense_amount')
            )
            ->where(function ($query) use ($branch) {
                if ($branch) {
                    $query->where('expenses.branch', $branch);
                }
            })
            ->groupBy(DB::raw('DATE_FORMAT(expenses.date, "%Y-%m")'));

        $sellsSubquery = DB::table('sells')
            ->join('invoices', 'sells.invoiceid', '=', 'invoices.id')
            ->select(
                DB::raw('DATE_FORMAT(sells.created_at, "%Y-%m") as month'),
                DB::raw('COUNT(sells.id) as total_sells'),
                DB::raw('SUM(CASE WHEN sells.category = "lab" THEN sells.sale_price * sells.product_qty ELSE 0 END) as total_buy_amount')
            )
            ->where('invoices.status', 'invoice')
            ->where(function ($query) use ($branch) {
                if ($branch) {
                    $query->where('invoices.branch', $branch);
                }
            })
            ->whereNull('invoices.deleted_at')
            ->groupBy(DB::raw('DATE_FORMAT(sells.created_at, "%Y-%m")'));

        $reports = DB::table(DB::raw("({$invoicesSubquery->toSql()}) as invoices"))
            ->mergeBindings($invoicesSubquery)
            ->leftJoin(DB::raw("({$expensesSubquery->toSql()}) as expenses"), 'invoices.month', '=', 'expenses.month')
            ->mergeBindings($expensesSubquery)
            ->leftJoin(DB::raw("({$sellsSubquery->toSql()}) as sells"), 'invoices.month', '=', 'sells.month')
            ->mergeBindings($sellsSubquery)
            ->select(
                'invoices.month',
                'invoices.qty',
                'invoices.total_income',
                'invoices.commission',
                DB::raw('COALESCE(sells.total_buy_amount, 0) as total_buy_amount'),
                DB::raw('COALESCE(expenses.total_expense_amount, 0) as total_expense_amount')
            )
            ->orderBy('invoices.month', 'desc')
            ->get();

        $branch_drop = Warehouse::all();
        $currentBranchName = $branch ? $branch_drop->where('id', $branch)->first()->name : 'All Branches';

        return view("report.profit", compact("reports", "branch_drop", "currentBranchName"));
    }


    public function profitSearch(Request $request)
    {

        if (auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            $branch = $request->input('branch');
        } else {
            $branch = auth()->user()->level;
        }

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $start_month = Carbon::parse($start_date)->format('Y-m');
        $end_month = Carbon::parse($end_date)->format('Y-m');

        $invoicesSubquery = DB::table('invoices')
            ->select(
                DB::raw('DATE_FORMAT(invoices.invoice_date, "%Y-%m") as month'),
                DB::raw('COUNT(invoices.id) as qty'),
                DB::raw('SUM(invoices.total) as total_income'),
                DB::raw('SUM(invoices.doctor_commission) as commission')
            )
            ->where('invoices.status', 'invoice')
            ->whereNull('invoices.deleted_at')
            ->whereBetween('invoices.invoice_date', [$start_date, $end_date])
            ->when($branch, function ($query, $branch) {
                $query->where('invoices.branch', $branch);
            })
            ->groupBy(DB::raw('DATE_FORMAT(invoices.invoice_date, "%Y-%m")'));

        $expensesSubquery = DB::table('expenses')
            ->select(
                DB::raw('DATE_FORMAT(expenses.date, "%Y-%m") as month'),
                DB::raw('SUM(expenses.amount) as total_expense_amount')
            )
            ->whereBetween('expenses.date', [$start_date, $end_date])
            ->when($branch, function ($query, $branch) {
                $query->where('expenses.branch', $branch);
            })
            ->groupBy(DB::raw('DATE_FORMAT(expenses.date, "%Y-%m")'));

        $sellsSubquery = DB::table('sells')
            ->join('invoices', 'sells.invoiceid', '=', 'invoices.id')
            ->select(
                DB::raw('DATE_FORMAT(sells.created_at, "%Y-%m") as month'),
                DB::raw('COUNT(sells.id) as total_sells'),
                DB::raw('SUM(CASE WHEN sells.category = "lab" THEN sells.sale_price * sells.product_qty ELSE 0 END) as total_buy_amount')
            )
            ->where('invoices.status', 'invoice')
            ->whereNull('invoices.deleted_at')
            ->whereBetween('sells.created_at', [$start_date, $end_date])
            ->when($branch, function ($query, $branch) {
                $query->where('invoices.branch', $branch);
            })
            ->groupBy(DB::raw('DATE_FORMAT(sells.created_at, "%Y-%m")'));

        $reports = DB::table(DB::raw("({$invoicesSubquery->toSql()}) as invoices"))
            ->mergeBindings($invoicesSubquery)
            ->leftJoin(DB::raw("({$expensesSubquery->toSql()}) as expenses"), 'invoices.month', '=', 'expenses.month')
            ->mergeBindings($expensesSubquery)
            ->leftJoin(DB::raw("({$sellsSubquery->toSql()}) as sells"), 'invoices.month', '=', 'sells.month')
            ->mergeBindings($sellsSubquery)
            ->select(
                'invoices.month',
                'invoices.qty',
                'invoices.total_income',
                'invoices.commission',
                DB::raw('COALESCE(sells.total_buy_amount, 0) as total_buy_amount'),
                DB::raw('COALESCE(expenses.total_expense_amount, 0) as total_expense_amount')
            )
            ->orderBy('invoices.month', 'desc')
            ->get();


        $branch_drop = Warehouse::all();


        $currentBranchName = $branch ? $branch_drop->where('id', $branch)->first()->name : 'All Branches';
        return view("report.profit", compact("reports", "branch_drop", "currentBranchName"));
    }
}
