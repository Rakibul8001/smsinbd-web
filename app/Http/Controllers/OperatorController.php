<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Core\OperatorsGateways\Operators as GatewayOperators;
use App\Divider;
use App\Http\Requests\OperatorRequest;
use App\Operators;
use Illuminate\Support\Facades\Auth;

class OperatorController extends Controller
{
    /**
     * Sms Operator Service
     *
     * @var App\Core\OperatorsGateways\OperatorsDetails
     */   
    protected $operator;

    public function __construct(Operators $operator)
    {
        $this->middleware('auth:root');

        $this->operator = $operator;
    }

    /**
     * Render add Operator form
     *
     * @return void
     */
    public function index() {
        return view('smsview.operators.index');
    }
    

    /**
     * Add sms operator
     *
     * @param Request $request
     * @return void
     */
    public function addOperator(OperatorRequest $request)
    {
        Operators::create([
            'name' => $request->name,
            'prefix' => $request->prefix,
            'type' => $request->type,
            'single_url' => $request->single_url,
            'multi_url' => $request->multi_url,
            'delivery_url' => $request->delivery_url,
            'active' =>1,
            'created_by' => Auth::guard('root')->user()->id
        ]);

        return back()->with('msg','Operator inserted successfully');
    }


    //edit operator
    public function editOperator(Request $request)
    {
        $operator = Operators::find($request->operator_id);

        if ($operator) {
            $active = 0;
            if ($request->active) {
                $active = 1;
            }

            $operator->name = $request->name;
            $operator->prefix = $request->prefix;
            $operator->type = $request->type;
            $operator->single_url = $request->single_url;
            $operator->multi_url = $request->multi_url;
            $operator->delivery_url = $request->delivery_url;
            $operator->active = $active;
            $operator->updated_by = Auth::guard('root')->user()->id;
            $operator->save();

            return back()->with('msg','Operator Updated Successfully');
        }

        return back()->with('errmsg','Invalid Request!');
    }

    

    public function showOperators()
    {
        $operators = Operators::all();
        return view('smsview.operators.operators-list', compact('operators'));
    }

    /**
     * get gateways of operator
     *
     * @param Request $request
     * @return void
     */
    public function getGetwayOfOperator(Request $request)
    {
        $operator = Operators::find($request->operator);
        if ($operator) {
            $gateways = $operator->operatorGateways;

            return json_encode($gateways);
        }

        return false;
    }


    //Edit divider
    public function editDivider(Request $request){
        $divider = Divider::latest()->first();
        // dd($divider);
        return view('smsview.divider.edit-divider',compact('divider'));
    }
    public function updateDivider(Request $request, $id){
        $divider = Divider::find($id);
        // dd($divider);
        $divider->divider = $request->divider;
        $divider->save();
        
        return back()->with('msg','Divider has been Updated Successfully');
    }


}
