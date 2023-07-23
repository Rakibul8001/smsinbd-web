<?php

namespace App\Http\Controllers;

use App\Core\ProductSales\ProductSales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientInvoiceListController extends Controller
{
    /**
     * Product sale service
     *
     * @var App\Core\ProductSale\ProductSaleDetails
     */
    protected $productsale;
    
    public function __construct(
        ProductSales $productsale
    )
    {
        $this->middleware('auth:root,web,manager,reseller');
        $this->productsale = $productsale;
    }
    

    /**
     * Root and manager invoice list
     *
     * @return void
     */
    public function clientInvoiceList() {

        return view('smsview.smssale.client-invoice-list');

    }

    public function showClientInvoices(Request $request)
    {
        $data = [
            'userid' => Auth::guard('web')->check() ? Auth::guard('web')->user()->id : @$request->userid,
        ];
        return $this->productsale->showClientInvoices($data);
    }


    /**
     * Root and manager invoice list
     *
     * @return void
     */
    public function resellerInvoiceList() {

        return view('smsview.smssale.reseller-invoicelist');

    }

    public function showResellerInvoices()
    {
        return $this->productsale->showResellerInvoices();
    }
}
