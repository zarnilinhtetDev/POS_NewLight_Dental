<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
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
use App\Http\Controllers\UserTypeController;
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
    Route::get('/migrate_status', function () {
        Artisan::call('migrate');
        return back()->with('success', 'Migration successful!');
    });
    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('items', [ItemController::class, 'index']);


    Route::get('items', [ItemController::class, 'index']);
    Route::post('items_register', [ItemController::class, 'register']);
    //for item search to add
    Route::get('/item_search', [InvoiceController::class, 'item_search'])->name('search_item_name_for_add');
    Route::post('/item_search_fill', [InvoiceController::class, 'item_data_search_fill'])->name('item_data_search_fill');


    //Patient
    Route::get('patient', [CustomerController::class, 'index']);
    Route::get('customer_credit/{id}', [CustomerController::class, 'credit']);
    Route::post('patient_register', [CustomerController::class, 'store']);
    Route::get('patient_edit/{id}', [CustomerController::class, 'edit']);
    Route::post('patient_update/{id}', [CustomerController::class, 'update']);
    Route::get('patient_delete/{id}', [CustomerController::class, 'delete']);

    //doctors
    Route::get('doctors', [SupplierController::class, 'index']);
    Route::post('doctors_register', [SupplierController::class, 'store']);
    Route::get('doctors_edit/{id}', [SupplierController::class, 'edit']);
    Route::post('doctors_update/{id}', [SupplierController::class, 'update']);
    Route::get('doctors_delete/{id}', [SupplierController::class, 'delete']);

    //Location
    Route::get('warehouse', [WarehouseController::class, 'index'])->name('warehouse');
    Route::post('warehouse_register', [WarehouseController::class, 'warehouse_register']);
    Route::get('warehouse_Delete/{id}', [WarehouseController::class, 'warehouse_delete']);
    Route::get('warehouse_Edit/{id}', [WarehouseController::class, 'warehouse_edit']);
    Route::post('warehouse_Update/{id}', [WarehouseController::class, 'warehouse_Update']);
    Route::get('transfer_item', [WarehouseController::class, 'transfer_item']);
    Route::post('store_transfer_item', [WarehouseController::class, 'store_transfer_item'])->name('store_transfer_item');
    Route::post('/autocomplete-part-code-location', [WarehouseController::class, 'autocompletePartCode'])->name('autocomplete.part-code-location');
    Route::post('/get-part-data-location', [WarehouseController::class, 'getPartData'])->name('get.part.data-location');
    Route::get('/show_transfer_history', [WarehouseController::class, 'show_history']);

    //Purchase Order
    Route::get('purchase_order_manage', [PurchaseOrderController::class, 'index'])->name('purchase_order');
    Route::get('purchase_order_register', [PurchaseOrderController::class, 'purchase_order_register'])->name('purchase_order_register');
    Route::post('purchase_order_store', [PurchaseOrderController::class, 'purchase_order_store'])->name('purchase_order_store');
    Route::get('purchase_order_delete/{id}', [PurchaseOrderController::class, 'po_delete'])->name('purchase_order_delete');
    Route::get('purchase_order_edit/{id}', [PurchaseOrderController::class, 'edit'])->name('purchase_order_edit');
    Route::post('purchase_order_update/{id}', [PurchaseOrderController::class, 'purchase_order_update'])->name('purchase_order_update');
    Route::get('purchase_order_details/{id}', [PurchaseOrderController::class, 'details'])->name('purchase_order_details');

    Route::post('/autocomplete_price', [App\Http\Controllers\PurchaseOrderController::class, 'autocomplete_price'])->name('autocomplete_price');
    Route::get('/customer_service_search', [App\Http\Controllers\PurchaseOrderController::class, 'po_search'])->name('po_search');
    Route::post('/customer_service_search_fill', [App\Http\Controllers\PurchaseOrderController::class, 'po_search_fill'])->name('po_search_fill');
    Route::post('/autocomplete-part-code', [PurchaseOrderController::class, 'autocompletePartCode'])->name('autocomplete.part-code');
    Route::post('/get-part-data', [PurchaseOrderController::class, 'getPartData'])->name('get.part.data');
    Route::get('/get-suppliers', [PurchaseOrderController::class, 'getSuppliers']);


    // Po Make Payment

    Route::get('po_make_payment/{id}', [PurchaseOrderController::class, 'po_payment'])->name('po_make_payment');
    Route::post('po_make_payment_store/{id}', [PurchaseOrderController::class, 'po_payment_store']);
    Route::get('po_cash_voucher_edit/{id}', [PurchaseOrderController::class, 'po_payment_edit']);
    Route::post('po_payment_update/{id}', [PurchaseOrderController::class, 'po_payment_update'])->name('po_payment_update');
    Route::get('po_cash_voucher/{make_payment}', [PurchaseOrderController::class, 'PovoucherView'])->name('po_voucher_view');




    //Invoice
    Route::get('invoice', [InvoiceController::class, 'index'])->name('invoice');
    // Route::get('customer_invoice', [InvoiceController::class, 'customer_invoice'])->middleware('isCashier')->name('customer_invoice');
    Route::get('customer_invoice/{customer_id?}', [InvoiceController::class, 'customer_invoice'])->name('customer_invoice');
    Route::post('invoice_register', [InvoiceController::class, 'invoice_register']);
    Route::get('invoice_reg', [InvoiceController::class, 'invoice']);
    Route::get('invoice_edit/{id}', [InvoiceController::class, 'invoice_edit']);
    Route::get('invoice_delete/{id}', [InvoiceController::class, 'invoice_delete']);
    Route::post('invoice_update/{id}', [InvoiceController::class, 'invoice_update']);
    Route::get('invoice_detail/{invoice}', [InvoiceController::class, 'invoice_detail'])->name('invoice_detail');
    Route::get('daily_sales', [InvoiceController::class, 'daily_sales'])->name('daily_sales');
    Route::get('invoice_edit_history', [InvoiceController::class, 'invoiceEditHistory'])->name('invoiceEditHistory');
    Route::get('invoice_receipt/{invoice}', [InvoiceController::class, 'invoice_receipt'])->name('invoice_receipt');
    Route::get('invoice_receipt/{invoice}', [InvoiceController::class, 'invoice_receipt'])->name('invoice_receipt');
    Route::get('invoice_daily_sales', [InvoiceController::class, 'invoice_daily_sales'])->name('invoice_daily_sales');
    Route::get('/get-doctors', [InvoiceController::class, 'getDoctors']);

    //Quotation
    Route::get('quotation', [InvoiceController::class, 'quotation']);
    Route::get('/quotation_detail/{id}', [InvoiceController::class, 'quotation_detail']);
    Route::get('quotation_register', [InvoiceController::class, 'quotation_register']);
    Route::get('/customer_service', [InvoiceController::class, 'customer_service_search'])->name('customer_service_search');
    Route::post('/customer_service', [InvoiceController::class, 'customer_service_search_fill'])->name('customer_service_search_fill');

    Route::get('/customer_phone', [InvoiceController::class, 'customer_phone_search'])->name('customer_phone_search');
    Route::post('/customer_phone', [InvoiceController::class, 'customer_phone_search_fill'])->name('customer_phone_search_fill');

    Route::get('quotation_delete/{id}', [InvoiceController::class, 'quotation_delete']);
    Route::get('quotation_edit/{id}', [InvoiceController::class, 'quotation_edit']);
    Route::get('change_invoice/{id}', [InvoiceController::class, 'change_invoice']);
    Route::post('/autocomplete-part-code-invoice', [InvoiceController::class, 'autocompletePartCode'])->name('autocomplete.part-code-invoice');
    Route::post('/get-part-data-invoice', [InvoiceController::class, 'getPartData'])->name('get.part.data-invoice');
    Route::post('/autocomplete-barcode-invoice', [InvoiceController::class, 'autocompleteBarCode'])->name('autocomplete.barcode-invoice');
    Route::post('/get-barcode-data-invoice', [InvoiceController::class, 'getBarcodeData'])->name('get.barcode.data-invoice');
    Route::post('/autocomplete-part-code', [InvoiceController::class, 'autocompletePartCodeInvoice'])->name('autocomplete-part-code-invoice');
    Route::post('/get-part-data', [InvoiceController::class, 'getPartDataInvoice'])->name('get-part-data-invoice');
    Route::get('admin_invoice_no_updates', [InvoiceController::class, 'admin_invoice_no_updates']);

    //makepayment
    Route::get('make_payment/{id}', [InvoiceController::class, 'payment'])->name('make_payment');
    Route::post('make_payment_store/{id}', [InvoiceController::class, 'payment_store']);
    // Route::get('payment_no_updates', [InvoiceController::class, 'payment_no_updates']);
    Route::get('cash_voucher_edit/{id}', [InvoiceController::class, 'payment_edit']);
    Route::post('payment_update/{id}', [InvoiceController::class, 'payment_update'])->name('payment_update');
    Route::get('cash_voucher/{make_payment}', [InvoiceController::class, 'voucherView'])->name('voucher_view');

    //item
    Route::get('items', [ItemController::class, 'index']);
    Route::get('items_register', [ItemController::class, 'register']);
    Route::post('item_store', [ItemController::class, 'store']);
    Route::get('item_details/{id}', [ItemController::class, 'details']);
    Route::get('item_edit/{id}', [ItemController::class, 'edit']);
    Route::post('item_update/{id}', [ItemController::class, 'update']);
    Route::get('item_delete/{id}', [ItemController::class, 'delete']);
    Route::get('barcode/{id}', [ItemController::class, 'barcode']);

    //inout
    Route::get('in_out/{id}', [ItemController::class, 'inout']);
    Route::post('in/{id}', [InOutController::class, 'in']);
    Route::post('out/{id}', [InOutController::class, 'out']);
    Route::get('/display_print/{items_id}/{id}', [InOutController::class, 'display_print'])->name('display_print');
    Route::get('invoice_record/{id}', [InOutController::class, 'invoice_record']);
    Route::get('purchase_record/{id}', [InOutController::class, 'purchase_order_reord']);
    Route::get('pos_record/{id}', [InOutController::class, 'pos_record']);

    //expense
    Route::get('expense', [ExpenseController::class, 'index']);
    Route::post('expense_store', [ExpenseController::class, 'expenseStore']);
    Route::get('expense_edit/{expense}', [ExpenseController::class, 'edit']);
    Route::post('expense_update/{expense}', [ExpenseController::class, 'update']);
    Route::get('expense_delete/{expense}', [ExpenseController::class, 'delete']);
    Route::get('get_part_data-unit', [ExpenseController::class, 'get_part_data_unit'])->name('get.part.data-unit');

    Route::get('expense_category', [ExpenseCategoryController::class, 'index']);
    Route::post('expense_category_store', [ExpenseCategoryController::class, 'categoryStore']);
    Route::get('expense_category_edit/{id}', [ExpenseCategoryController::class, 'edit']);
    Route::post('expense_category_update/{id}', [ExpenseCategoryController::class, 'update']);
    Route::get('expense_category_delete/{id}', [ExpenseCategoryController::class, 'delete']);
    Route::get('/get-categories', [ExpenseController::class, 'getCategory']);

    //POS
    Route::get('pos_register', [InvoiceController::class, 'pos_register']);
    Route::get('pos_daily_sales', [InvoiceController::class, 'pos_daily_sales'])->name('pos_daily_sales');
    Route::get('pos', [InvoiceController::class, 'pos']);
    Route::post('/autocomplete-part-code-invoice', [InvoiceController::class, 'autocompletePartCode'])->name('autocomplete.part-code-invoice');
    Route::post('/get-part-data-invoice', [InvoiceController::class, 'getPartData'])->name('get.part.data-invoice');
    Route::post('/autocomplete-barcode-invoice', [InvoiceController::class, 'autocompleteBarCode'])->name('autocomplete.barcode-invoice');
    Route::post('/get-barcode-data-invoice', [InvoiceController::class, 'getBarcodeData'])->name('get.barcode.data-invoice');
    Route::get('pos_delete/{id}', [InvoiceController::class, 'pos_delete']);
    Route::post('/suspended', [InvoiceController::class, 'suspended'])->name('suspended');
    Route::get('/suspend_delete/{id}', [InvoiceController::class, 'suspend_delete']);

    //report
    Route::get('report', [ReportController::class, 'report_invoice']);
    Route::get('report_item', [ReportController::class, 'report_item']);
    Route::get('report_clinic_item', [ReportController::class, 'report_clinic_item']);
    Route::get('report_expense', [ReportController::class, 'reportExpense']);
    Route::get('doctor', [ReportController::class, 'doctor']);
    Route::get('doctorDetail/{id}', [ReportController::class, 'doctorDetail']);
    Route::get('/profit', [ReportController::class, 'profit'])->name('profit');
    Route::get('monthly_purchase_return', [ReportController::class, 'monthly_purchase_return']);
    Route::get('monthly_invoice_search', [ReportController::class, 'monthly_invoice_search']);
    Route::get('monthly_sale_return', [ReportController::class, 'monthly_sale_return']);
    Route::get('monthly_quotation_search', [ReportController::class, 'monthly_quotation_search']);
    Route::get('monthly_po_search', [ReportController::class, 'monthly_po_search']);
    Route::get('monthly_pos_search', [ReportController::class, 'monthly_pos_search']);
    Route::get('expense_search', [ReportController::class, 'expenseSearch']);
    Route::get('invoice_search', [ReportController::class, 'invoiceSearch']);
    // Route::get('doctor_search', [ReportController::class, 'doctorSearch'])->middleware('isAdmin');
    Route::get('doctor_search', [ReportController::class, 'doctorSearch']);
    Route::get('item_search', [ReportController::class, 'itemSearch']);
    Route::get('clinic_item_search', [ReportController::class, 'ClinicItemSearch']);
    Route::get('doctorDetailSearch/{id}', [ReportController::class, 'doctorDetailSearch']);
    Route::get('/profit/search',  [ReportController::class, 'profitSearch'])->name('profitSearch');


    //Excel_Item_Export & Import
    Route::get('file-import-export', [ItemController::class, 'fileImportExport']);
    Route::post('file-import', [ItemController::class, 'fileImport'])->name('file-import');
    Route::get('file-export', [ItemController::class, 'fileExport'])->name('file-export');
    Route::get('file-import-template', [ItemController::class, 'fileImportTemplate'])->name('file-import-template');

    Route::get('user', [UserController::class, 'user_register'])->name('user');
    Route::post('User_Register', [UserController::class, 'user_store']);
    Route::get('/delete_user/{id}', [UserController::class, 'delete_user']);
    Route::get('/delete_user/{id}', [UserController::class, 'delete_user']);
    Route::get('/userShow/{id}', [UserController::class, 'userShow']);
    Route::post('/update_user/{id}', [UserController::class, 'update_user']);
    Route::post('/drop_table', [ItemController::class, 'drop_table'])->name('drop.table');


    //Test
    Route::get('/report_invoice/{branch?}', [ReportController::class, 'report_invoice'])->name('report_invoice');
    Route::get('/report_exp/{branch?}', [ReportController::class, 'reportExpense'])->name('report_exp');
    Route::get('doctor_report/{branch?}', [ReportController::class, 'doctor'])->name('doctor_report');
    Route::get('/profit/{branch}', [ReportController::class, 'profit'])->name('profit.branch');


    //User Type
    Route::get('user_type', [UserTypeController::class, 'index']);
    Route::post('type_store', [UserTypeController::class, 'store']);
    Route::get('user_type_edit/{id}', [UserTypeController::class, 'edit']);
    Route::post('user_type_update/{id}', [UserTypeController::class, 'update']);
    Route::get('user_type_delete/{id}', [UserTypeController::class, 'delete']);

    Route::get('user_permission/{id}', [UserController::class, 'permission']);
    Route::post('user_permission_store/{id}', [UserController::class, 'permissionStore']);
});
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

//update
// Route::get('invoice_commission_update', [InvoiceController::class, 'allInvoiceCommissionUpdate']);

require __DIR__ . '/auth.php';
