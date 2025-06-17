<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Carbon;
use App\Models\TransferHistory;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $selectedWarehouseId = $request->query('warehouse');

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $invoiceCount = Invoice::where('status', 'invoice')
                ->where('invoice_category', 'Invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->count();

            $posCount = Invoice::where('status', 'pos')
                ->where('invoice_category', 'POS')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->count();


            $quotationCount = Invoice::where('status', 'quotation')
                ->where('invoice_category', 'Invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->count();

            $customerCount = Customer::when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                return $query->where('branch', $selectedWarehouseId); // Assuming 'branch' column exists
            })
                ->count();



            $monthlyCustomerCount = Customer::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId); // Assuming 'branch' column exists
                })
                ->count();





            $purchaseOrderCount = PurchaseOrder::where('balance_due', 'PO')
                ->where('status', 'invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->count();

            $monthNames = [
                1 => 'Jan',
                2 => 'Feb',
                3 => 'Mar',
                4 => 'Apr',
                5 => 'May',
                6 => 'Jun',
                7 => 'Jul',
                8 => 'Aug',
                9 => 'Sep',
                10 => 'Oct',
                11 => 'Nov',
                12 => 'Dec'
            ];




            $chart = [];
            $posChart = [];
            $poChart = [];

            // Fetch invoices for the current year
            $invoices = Invoice::where('status', 'invoice')
                ->where('invoice_category', 'Invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)  // Get all months of this year
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->get();

            $quotation = Invoice::where('status', 'quotation')
                ->where('invoice_category', 'Invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)  // Get all months of this year
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->get();

            // Loop through invoices and sum totals per month
            foreach ($invoices as $invoice) {
                $monthIndex = $invoice->created_at->month; // Get numeric month (1-12)
                $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                // Sum total amounts per month
                if (!isset($chart[$month])) {
                    $chart[$month] = 0; // Initialize if not set
                }
                $chart[$month] += $invoice->total;
            }

            // dd($chart);

            // Fetch invoices for the current year
            $point_of_sales = Invoice::where('status', 'pos')
                ->where('invoice_category', 'POS')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)  // Get all months of this year
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->get();

            // Loop through invoices and sum totals per month
            foreach ($point_of_sales as $pos) {
                $monthIndex = $pos->created_at->month; // Get numeric month (1-12)
                $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                // Sum total amounts per month
                if (!isset($posChart[$month])) {
                    $posChart[$month] = 0; // Initialize if not set
                }
                $posChart[$month] += $pos->total;
            }

            // Fetch invoices for the current year
            $purchase_orders = PurchaseOrder::where('balance_due', 'PO')
                ->where('status', 'invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)  // Get all months of this year
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->get();

            // Loop through invoices and sum totals per month
            foreach ($purchase_orders as $po) {
                $monthIndex = $po->created_at->month; // Get numeric month (1-12)
                $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                // Sum total amounts per month
                if (!isset($poChart[$month])) {
                    $poChart[$month] = 0; // Initialize if not set
                }
                $poChart[$month] += $po->total;
            }

            $warehouses = Warehouse::all();
            $warehouseNames = $warehouses->pluck('name');


            $transfer_out_data = $warehouses->map(function ($warehouse) {
                return TransferHistory::where('from_location', $warehouse->id)
                    ->whereMonth('created_at', Carbon::now()->month)  // Filter for the current month
                    ->whereYear('created_at', Carbon::now()->year)   // Filter for the current year
                    ->sum('quantity');
            });

            $transfer_in_data = $warehouses->map(function ($warehouse) {
                return TransferHistory::where('to_location', $warehouse->id)
                    ->whereMonth('created_at', Carbon::now()->month)  // Filter for the current month
                    ->whereYear('created_at', Carbon::now()->year)   // Filter for the current year
                    ->sum('quantity');
            });
        } else {
            $invoiceCount = Invoice::where('status', 'invoice')
                ->where('invoice_category', 'Invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->whereIn('branch', $warehousePermission)
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->count();


            $posCount = Invoice::where('status', 'pos')
                ->where('invoice_category', 'POS')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->whereIn('branch', $warehousePermission)
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->count();

            $quotationCount = Invoice::where('status', 'quotation')
                ->where('invoice_category', 'Invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->whereIn('branch', $warehousePermission)
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->count();

            $customerCount = Customer::whereIn('branch', $warehousePermission)
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->count();


            $monthlyCustomerCount = Customer::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->whereIn('branch', $warehousePermission)
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->count();





            $purchaseOrderCount = PurchaseOrder::where('balance_due', 'PO')
                ->where('status', 'invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->whereIn('branch', $warehousePermission)
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->count();


            $monthNames = [
                1 => 'Jan',
                2 => 'Feb',
                3 => 'Mar',
                4 => 'Apr',
                5 => 'May',
                6 => 'Jun',
                7 => 'Jul',
                8 => 'Aug',
                9 => 'Sep',
                10 => 'Oct',
                11 => 'Nov',
                12 => 'Dec',
            ];




            $chart = [];
            $posChart = [];
            $poChart = [];

            // Fetch invoices for the current year
            $invoices = Invoice::where('status', 'invoice')
                ->where('invoice_category', 'Invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->where('branch', $warehousePermission)
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->get();

            $quotation = Invoice::where('status', 'quotation')
                ->where('invoice_category', 'Invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->where('branch', $warehousePermission)
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->get();

            // Loop through invoices and sum totals per month
            foreach ($invoices as $invoice) {
                $monthIndex = $invoice->created_at->month; // Get numeric month (1-12)
                $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                // Sum total amounts per month
                if (!isset($chart[$month])) {
                    $chart[$month] = 0; // Initialize if not set
                }
                $chart[$month] += $invoice->total;
            }

            // Fetch point of sales for the current year
            $point_of_sales = Invoice::where('status', 'pos')
                ->where('invoice_category', 'POS')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->where('branch', $warehousePermission)
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->get();

            // Loop through point of sales and sum totals per month
            foreach ($point_of_sales as $pos) {
                $monthIndex = $pos->created_at->month; // Get numeric month (1-12)
                $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                // Sum total amounts per month
                if (!isset($posChart[$month])) {
                    $posChart[$month] = 0; // Initialize if not set
                }
                $posChart[$month] += $pos->total;
            }

            // Fetch purchase orders for the current year
            $purchase_orders = PurchaseOrder::where('balance_due', 'PO')
                ->where('status', 'invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->whereIn('branch', $warehousePermission)
                ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                    return $query->where('branch', $selectedWarehouseId);
                })
                ->get();

            // Loop through purchase orders and sum totals per month
            foreach ($purchase_orders as $po) {
                $monthIndex = $po->created_at->month; // Get numeric month (1-12)
                $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                // Sum total amounts per month
                if (!isset($poChart[$month])) {
                    $poChart[$month] = 0; // Initialize if not set
                }
                $poChart[$month] += $po->total;
            }

            $warehouses = Warehouse::where('id', $warehousePermission)->get();
            $warehouseNames = $warehouses->pluck('name');

            $transfer_out_data = $warehouses->map(function ($warehouse) {
                return TransferHistory::where('from_location', $warehouse->id)
                    ->whereMonth('created_at', Carbon::now()->month)  // Filter for the current month
                    ->whereYear('created_at', Carbon::now()->year)   // Filter for the current year
                    ->sum('quantity');
            });

            $transfer_in_data = $warehouses->map(function ($warehouse) {
                return TransferHistory::where('to_location', $warehouse->id)
                    ->whereMonth('created_at', Carbon::now()->month)  // Filter for the current month
                    ->whereYear('created_at', Carbon::now()->year)   // Filter for the current year
                    ->sum('quantity');
            });
        }


        // dd($chart);


        return view('dashboard.dashboard', compact('invoiceCount', 'posCount', 'quotationCount', 'purchaseOrderCount', 'customerCount', 'monthlyCustomerCount',  'chart', 'posChart', 'poChart', 'warehouses', 'warehouseNames', 'invoices', 'point_of_sales', 'purchase_orders', 'quotation', 'transfer_out_data', 'transfer_in_data'));
    }
}
