<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\GateWayFormRequest;
use App\Core\OperatorsGateways\OperatorsApi;

use App\Operators;
use App\OperatorGateways;

class GatewayController extends Controller
{
    protected $gateway;
    
    public function __construct(OperatorsApi $gateway)
    {
        $this->middleware('auth:root');
        $this->gateway = $gateway;
    }

    public function addOperatorGateway(Request $request)
    {

        if (!$request->operator_id) {
            return back()->with(['errmsg' => 'Operator is Required']);
        }

        if ($request->gateway_id) {
            $gateway = OperatorGateways::find($request->gateway_id);

            $active = 0;
            if ($request->active) {
                $active = 1;
            }

            $gateway->operator_id = $request->operator_id;
            $gateway->name = $request->gateway_name;
            $gateway->username = $request->username;
            $gateway->password = $request->password;
            $gateway->updated_by = Auth::guard('root')->user()->id;
            $gateway->active = $active;
            $gateway->save();

            return back()->with(['msg' => 'Gateway updated successfully']);
        } else {
            $gateways = OperatorGateways::create([
                'operator_id'   => $request->operator_id,
                'name'          => $request->gateway_name,
                'username'      => $request->username,
                'password'      => $request->password,
                'created_by'    => Auth::guard('root')->user()->id,
                'active'        => 1,
            ]);
            return back()->with(['msg' => 'Gateway created successfully']);
        }
        
    }

    public function getApiInformation(Request $request)
    {
        if (! $request->has('id'))
        {
            return back()->with('errmsg','ID validation faild');
        }
        return $this->gateway->getGatewayApi($request->id);
    }

    public function showGateways()
    {

        $operators = Operators::where('active', 1)->get();
        $gateways = OperatorGateways::get();

        return view('smsview.operators.gateway-list', compact('operators', 'gateways'));
    }

    public function renderGateways()
    {
        return $this->gateway->showApiGateways();
    }

    public function getGateways()
    {
        return $this->getGateways->getGateways();
    }

    protected function validateOperatorGateways()
    {
        return request()->validate([
            'operator_id'   => ['required','integer'],
            'gateway_name'  => 'max:255',
            'username'      => 'max:100',
            'password'      => 'max:255',
        ]);
    }
}
