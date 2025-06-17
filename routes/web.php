<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InOutController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ExpenseCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('items', [ItemController::class, 'index']);


    Route::get('items', [ItemController::class, 'index']);
    Route::post('items_register', [ItemController::class, 'register']);
    //for item search to add
    Route::get('/item_search', [InvoiceController::class, 'item_search'])->name('search_item_name_for_add');
    Route::post('/item_search_fill', [InvoiceController::class, 'item_data_search_fill'])->name('item_data_search_fill');


    //Patient
    Route::get('patient', [CustomerController::class, 'index'])->middleware('isCashier');
    Route::get('customer_credit/{id}', [CustomerController::class, 'credit'])->middleware('isCashier');
    Route::post('patient_register', [CustomerController::class, 'store'])->middleware('isCashier');
    Route::get('patient_edit/{id}', [CustomerController::class, 'edit'])->middleware('isCashier');
    Route::post('patient_update/{id}', [CustomerController::class, 'update'])->middleware('isCashier');
    Route::get('patient_delete/{id}', [CustomerController::class, 'delete'])->middleware('isBranchManager');

    //doctors
    Route::get('doctors', [SupplierController::class, 'index'])->middleware('isCashier');
    Route::post('doctors_register', [SupplierController::class, 'store'])->middleware('isCashier');
    Route::get('doctors_edit/{id}', [SupplierController::class, 'edit'])->middleware('isCashier');
    Route::post('doctors_update/{id}', [SupplierController::class, 'update'])->middleware('isCashier');
    Route::get('doctors_delete/{id}', [SupplierController::class, 'delete'])->middleware('isBranchManager');

    //Location
    Route::get('warehouse', [WarehouseController::class, 'index'])->name('warehouse')->middleware('isBranchManager');
    Route::post('warehouse_register', [WarehouseController::class, 'warehouse_register'])->middleware('isAdmin');
    Route::get('warehouse_Delete/{id}', [WarehouseController::class, 'warehouse_delete'])->middleware('isAdmin');
    Route::get('warehouse_Edit/{id}', [WarehouseController::class, 'warehouse_edit'])->middleware('isAdmin');
    Route::post('warehouse_Update/{id}', [WarehouseController::class, 'warehouse_Update'])->middleware('isAdmin');
    Route::get('transfer_item', [WarehouseController::class, 'transfer_item'])->middleware('isBranchManager');
    Route::post('store_transfer_item', [WarehouseController::class, 'store_transfer_item'])->name('store_transfer_item')->middleware('isBranchManager');
    Route::post('/autocomplete-part-code-location', [WarehouseController::class, 'autocompletePartCode'])->name('autocomplete.part-code-location');
    Route::post('/get-part-data-location', [WarehouseController::class, 'getPartData'])->name('get.part.data-location');
    Route::get('/show_transfer_history', [WarehouseController::class, 'show_history'])->middleware('isBranchManager');

    //Purchase Order
    Route::get('purchase_order_manage', [PurchaseOrderController::class, 'index'])->name('purchase_order')->middleware('isBranchManager');
    Route::get('purchase_order_register', [PurchaseOrderController::class, 'purchase_order_register'])->name('purchase_order_register')->middleware('isBranchManager');
    Route::post('purchase_order_store', [PurchaseOrderController::class, 'purchase_order_store'])->name('purchase_order_store')->middleware('isBranchManager');
    Route::get('purchase_order_delete/{id}', [PurchaseOrderController::class, 'po_delete'])->name('purchase_order_delete')->middleware('isBranchManager');
    Route::get('purchase_order_edit/{id}', [PurchaseOrderController::class, 'edit'])->name('purchase_order_edit')->middleware('isBranchManager');
    Route::post('purchase_order_update/{id}', [PurchaseOrderController::class, 'purchase_order_update'])->name('purchase_order_update')->middleware('isBranchManager');
    Route::get('purchase_order_details/{id}', [PurchaseOrderController::class, 'details'])->name('purchase_order_details')->middleware('isBranchManager');

    Route::post('/autocomplete_price', [App\Http\Controllers\PurchaseOrderController::class, 'autocomplete_price'])->name('autocomplete_price');
    Route::get('/customer_service_search', [App\Http\Controllers\PurchaseOrderController::class, 'po_search'])->name('po_search');
    Route::post('/customer_service_search_fill', [App\Http\Controllers\PurchaseOrderController::class, 'po_search_fill'])->name('po_search_fill');
    Route::post('/autocomplete-part-code', [PurchaseOrderController::class, 'autocompletePartCode'])->name('autocomplete.part-code');
    Route::post('/get-part-data', [PurchaseOrderController::class, 'getPartData'])->name('get.part.data');


    // Po Make Payment

    Route::get('po_make_payment/{id}', [PurchaseOrderController::class, 'po_payment'])->name('po_make_payment');
    Route::post('po_make_payment_store/{id}', [PurchaseOrderController::class, 'po_payment_store']);
    Route::get('po_cash_voucher_edit/{id}', [PurchaseOrderController::class, 'po_payment_edit']);
    Route::post('po_payment_update/{id}', [PurchaseOrderController::class, 'po_payment_update'])->name('po_payment_update');
    Route::get('po_cash_voucher/{make_payment}', [PurchaseOrderController::class, 'PovoucherView'])->name('po_voucher_view');




    //Invoice
    Route::get('invoice', [InvoiceController::class, 'index'])->middleware('isCashier')->name('invoice');
    // Route::get('customer_invoice', [InvoiceController::class, 'customer_invoice'])->middleware('isCashier')->name('customer_invoice');
    Route::get('customer_invoice/{customer_id?}', [InvoiceController::class, 'customer_invoice'])
        ->middleware('isCashier')
        ->name('customer_invoice');
    Route::post('invoice_register', [InvoiceController::class, 'invoice_register'])->middleware('isCashier');
    Route::get('invoice_reg', [InvoiceController::class, 'invoice'])->middleware('isCashier');
    Route::get('invoice_edit/{id}', [InvoiceController::class, 'invoice_edit'])->middleware('isCashier');
    Route::get('invoice_delete/{id}', [InvoiceController::class, 'invoice_delete'])->middleware('isBranchManager');
    Route::post('invoice_update/{id}', [InvoiceController::class, 'invoice_update'])->middleware('isCashier');
    Route::get('invoice_detail/{invoice}', [InvoiceController::class, 'invoice_detail'])->name('invoice_detail')->middleware('isCashier');
    Route::get('daily_sales', [InvoiceController::class, 'daily_sales'])->name('daily_sales')->middleware('isCashier');
    Route::get('invoice_edit_history', [InvoiceController::class, 'invoiceEditHistory'])->name('invoiceEditHistory');
    Route::get('invoice_receipt/{invoice}', [InvoiceController::class, 'invoice_receipt'])->name('invoice_receipt')->middleware('isCashier');
    Route::get('invoice_receipt/{invoice}', [InvoiceController::class, 'invoice_receipt'])->name('invoice_receipt')->middleware('isCashier');
    Route::get('invoice_daily_sales', [InvoiceController::class, 'invoice_daily_sales'])->name('invoice_daily_sales')->middleware('isCashier');

    //Quotation
    Route::get('quotation', [InvoiceController::class, 'quotation'])->middleware('isBranchManager');
    Route::get('/quotation_detail/{id}', [InvoiceController::class, 'quotation_detail'])->middleware('isBranchManager');
    Route::get('quotation_register', [InvoiceController::class, 'quotation_register'])->middleware('isBranchManager');
    Route::get('/customer_service', [InvoiceController::class, 'customer_service_search'])->name('customer_service_search');
    Route::post('/customer_service', [InvoiceController::class, 'customer_service_search_fill'])->name('customer_service_search_fill');

    Route::get('/customer_phone', [InvoiceController::class, 'customer_phone_search'])->name('customer_phone_search');
    Route::post('/customer_phone', [InvoiceController::class, 'customer_phone_search_fill'])->name('customer_phone_search_fill');

    Route::get('quotation_delete/{id}', [InvoiceController::class, 'quotation_delete'])->middleware('isBranchManager');
    Route::get('quotation_edit/{id}', [InvoiceController::class, 'quotation_edit'])->middleware('isBranchManager');
    Route::get('change_invoice/{id}', [InvoiceController::class, 'change_invoice'])->middleware('isBranchManager');
    Route::post('/autocomplete-part-code-invoice', [InvoiceController::class, 'autocompletePartCode'])->name('autocomplete.part-code-invoice');
    Route::post('/get-part-data-invoice', [InvoiceController::class, 'getPartData'])->name('get.part.data-invoice');
    Route::post('/autocomplete-barcode-invoice', [InvoiceController::class, 'autocompleteBarCode'])->name('autocomplete.barcode-invoice');
    Route::post('/get-barcode-data-invoice', [InvoiceController::class, 'getBarcodeData'])->name('get.barcode.data-invoice');
    Route::post('/autocomplete-part-code', [InvoiceController::class, 'autocompletePartCodeInvoice'])->name('autocomplete-part-code-invoice');
    Route::post('/get-part-data', [InvoiceController::class, 'getPartDataInvoice'])->name('get-part-data-invoice');

    //makepayment
    Route::get('make_payment/{id}', [InvoiceController::class, 'payment'])->name('make_payment');
    Route::post('make_payment_store/{id}', [InvoiceController::class, 'payment_store']);
    // Route::get('payment_no_updates', [InvoiceController::class, 'payment_no_updates']);
    Route::get('cash_voucher_edit/{id}', [InvoiceController::class, 'payment_edit']);
    Route::post('payment_update/{id}', [InvoiceController::class, 'payment_update'])->name('payment_update');
    Route::get('cash_voucher/{make_payment}', [InvoiceController::class, 'voucherView'])->name('voucher_view');

    //item
    Route::get('items', [ItemController::class, 'index'])->middleware('isCashier');
    Route::get('items_register', [ItemController::class, 'register'])->middleware('isCashier');
    Route::post('item_store', [ItemController::class, 'store'])->middleware('isCashier');
    Route::get('item_details/{id}', [ItemController::class, 'details'])->middleware('isCashier');
    Route::get('item_edit/{id}', [ItemController::class, 'edit'])->middleware('isCashier');
    Route::post('item_update/{id}', [ItemController::class, 'update'])->middleware('isCashier');
    Route::get('item_delete/{id}', [ItemController::class, 'delete'])->middleware('isBranchManager');
    Route::get('barcode/{id}', [ItemController::class, 'barcode'])->middleware('isCashier');

    //inout
    Route::get('in_out/{id}', [ItemController::class, 'inout'])->middleware('isBranchManager');
    Route::post('in/{id}', [InOutController::class, 'in'])->middleware('isBranchManager');
    Route::post('out/{id}', [InOutController::class, 'out'])->middleware('isBranchManager');
    Route::get('/display_print/{items_id}/{id}', [InOutController::class, 'display_print'])->name('display_print')->middleware('isBranchManager');
    Route::get('invoice_record/{id}', [InOutController::class, 'invoice_record'])->middleware('isBranchManager');
    Route::get('purchase_record/{id}', [InOutController::class, 'purchase_order_reord'])->middleware('isBranchManager');
    Route::get('pos_record/{id}', [InOutController::class, 'pos_record'])->middleware('isBranchManager');

    //expense
    Route::get('expense', [ExpenseController::class, 'index'])->middleware('isCashier');
    Route::post('expense_store', [ExpenseController::class, 'expenseStore'])->middleware('isCashier');
    Route::get('expense_edit/{expense}', [ExpenseController::class, 'edit'])->middleware('isCashier');
    Route::post('expense_update/{expense}', [ExpenseController::class, 'update'])->middleware('isCashier');
    Route::get('expense_delete/{expense}', [ExpenseController::class, 'delete'])->middleware('isBranchManager');
    Route::get('get_part_data-unit', [ExpenseController::class, 'get_part_data_unit'])->name('get.part.data-unit');

    Route::get('expense_category', [ExpenseCategoryController::class, 'index'])->middleware('isCashier');
    Route::post('expense_category_store', [ExpenseCategoryController::class, 'categoryStore'])->middleware('isCashier');
    Route::get('expense_category_edit/{id}', [ExpenseCategoryController::class, 'edit'])->middleware('isCashier');
    Route::post('expense_category_update/{id}', [ExpenseCategoryController::class, 'update'])->middleware('isCashier');
    Route::get('expense_category_delete/{id}', [ExpenseCategoryController::class, 'delete'])->middleware('isBranchManager');

    //POS
    Route::get('pos_register', [InvoiceController::class, 'pos_register'])->middleware('isCashier');
    Route::get('pos_daily_sales', [InvoiceController::class, 'pos_daily_sales'])->name('pos_daily_sales')->middleware('isCashier');
    Route::get('pos', [InvoiceController::class, 'pos'])->middleware('isCashier');
    Route::post('/autocomplete-part-code-invoice', [InvoiceController::class, 'autocompletePartCode'])->name('autocomplete.part-code-invoice');
    Route::post('/get-part-data-invoice', [InvoiceController::class, 'getPartData'])->name('get.part.data-invoice');
    Route::post('/autocomplete-barcode-invoice', [InvoiceController::class, 'autocompleteBarCode'])->name('autocomplete.barcode-invoice');
    Route::post('/get-barcode-data-invoice', [InvoiceController::class, 'getBarcodeData'])->name('get.barcode.data-invoice');
    Route::get('pos_delete/{id}', [InvoiceController::class, 'pos_delete'])->middleware('isBranchManager');
    Route::post('/suspended', [InvoiceController::class, 'suspended'])->name('suspended')->middleware('isCashier');
    Route::get('/suspend_delete/{id}', [InvoiceController::class, 'suspend_delete'])->middleware('isCashier');

    //report
    Route::get('report', [ReportController::class, 'report_invoice'])->middleware('isBranchManager');
    Route::get('report_item', [ReportController::class, 'report_item'])->middleware('isBranchManager');
    Route::get('report_clinic_item', [ReportController::class, 'report_clinic_item'])->middleware('isBranchManager');
    Route::get('report_expense', [ReportController::class, 'reportExpense'])->middleware('isBranchManager');
    Route::get('doctor', [ReportController::class, 'doctor'])->middleware('isBranchManager');
    Route::get('doctorDetail/{id}', [ReportController::class, 'doctorDetail'])->middleware('isBranchManager');
    Route::get('/profit', [ReportController::class, 'profit'])->name('profit')->middleware('isBranchManager');
    Route::get('monthly_purchase_return', [ReportController::class, 'monthly_purchase_return'])->middleware('isBranchManager');
    Route::get('monthly_invoice_search', [ReportController::class, 'monthly_invoice_search'])->middleware('isBranchManager');
    Route::get('monthly_sale_return', [ReportController::class, 'monthly_sale_return'])->middleware('isBranchManager');
    Route::get('monthly_quotation_search', [ReportController::class, 'monthly_quotation_search'])->middleware('isBranchManager');
    Route::get('monthly_po_search', [ReportController::class, 'monthly_po_search'])->middleware('isBranchManager');
    Route::get('monthly_pos_search', [ReportController::class, 'monthly_pos_search'])->middleware('isBranchManager');
    Route::get('expense_search', [ReportController::class, 'expenseSearch'])->middleware('isBranchManager');
    Route::get('invoice_search', [ReportController::class, 'invoiceSearch'])->middleware('isBranchManager');
    // Route::get('doctor_search', [ReportController::class, 'doctorSearch'])->middleware('isAdmin');
    Route::get('doctor_search', [ReportController::class, 'doctorSearch'])->middleware('isBranchManager');
    Route::get('item_search', [ReportController::class, 'itemSearch'])->middleware('isBranchManager');
    Route::get('clinic_item_search', [ReportController::class, 'ClinicItemSearch'])->middleware('isBranchManager');
    Route::get('doctorDetailSearch/{id}', [ReportController::class, 'doctorDetailSearch'])->middleware('isBranchManager');
    Route::get('/profit/search',  [ReportController::class, 'profitSearch'])->name('profitSearch');


    //Excel_Item_Export & Import
    Route::get('file-import-export', [ItemController::class, 'fileImportExport']);
    Route::post('file-import', [ItemController::class, 'fileImport'])->name('file-import');
    Route::get('file-export', [ItemController::class, 'fileExport'])->name('file-export');
    Route::get('file-import-template', [ItemController::class, 'fileImportTemplate'])->name('file-import-template');

    Route::get('user', [UserController::class, 'user_register'])->name('user')->middleware('isBranchManager');
    Route::post('User_Register', [UserController::class, 'user_store'])->middleware('isBranchManager');
    Route::get('/delete_user/{id}', [UserController::class, 'delete_user'])->middleware('isBranchManager');
    Route::get('/delete_user/{id}', [UserController::class, 'delete_user'])->middleware('isBranchManager');
    Route::get('/userShow/{id}', [UserController::class, 'userShow'])->middleware('isBranchManager');
    Route::post('/update_user/{id}', [UserController::class, 'update_user'])->middleware('isBranchManager');
    Route::post('/drop_table', [ItemController::class, 'drop_table'])->name('drop.table');


    //Test
    Route::get('/report_invoice/{branch?}', [ReportController::class, 'report_invoice'])->name('report_invoice');
    Route::get('/report_exp/{branch?}', [ReportController::class, 'reportExpense'])->name('report_exp');
    Route::get('doctor_report/{branch?}', [ReportController::class, 'doctor'])->name('doctor_report');
    Route::get('/profit/{branch}', [ReportController::class, 'profit'])->name('profit.branch');
});
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

//update
// Route::get('invoice_commission_update', [InvoiceController::class, 'allInvoiceCommissionUpdate']);

require __DIR__ . '/auth.php';
