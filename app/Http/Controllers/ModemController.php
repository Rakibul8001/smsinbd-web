<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Modem;
use Illuminate\Support\Facades\Auth;

class ModemController extends Controller
{
    /**
     * Sms Operator Service
     *
     * @var App\Core\OperatorsGateways\OperatorsDetails
     */   
    protected $modem;

    public function __construct(Modem $modem)
    {
        $this->middleware('auth:root');

        $this->modem = $modem;
    }

    /**
     * Render manage modem
     *
     * @return void
     */
    public function manageModems() {
        $modems = Modem::all();
        return view('smsview.modems.manage-modems', compact('modems'));
    }

    /**
     * Render add Operator form
     *
     * @return void
     */
    public function addModem() {
        // $modems = Modem::all();
        return view('smsview.modems.index');
    }
    

    /**
     * Add sms operator
     *
     * @param Request $request
     * @return void
     */
    public function addModemPost(Request $request)
    {

        $api_token = Str::random(40);

        Modem::create([
            'name' => $request->name,
            'sim_number' => $request->sim_number,
            'description' => $request->description,
            'created_by' => Auth::guard('root')->user()->id,
            'api_token' => $api_token,
            'status' => 1,
            'active' => 1,
        ]);

        return back()->with('msg','Modem created successfully');
    }


    //edit modem
    public function editModemPost(Request $request)
    {
        $modem = Modem::find($request->modem_id);

        if ($modem) {
            $active = 0;
            if ($request->active) {
                $active = 1;
            }

            $modem->name = $request->name;
            $modem->sim_number = $request->sim_number;
            $modem->description = $request->description;
            $modem->active = $active;
            $modem->updated_by = Auth::guard('root')->user()->id;
            $modem->save();

            return back()->with('msg','Modem Updated Successfully');
        }

        return back()->with('errmsg','Invalid Request!');
    }

    //regenarate api key of modem
    public function regenerateModemToken($id)
    {
        $api_token = Str::random(40);
        $modem = Modem::find($id);
        if ($modem)
        {
            $modem->api_token = $api_token;
            $modem->save();
        }

        return back()->with('msg', 'Token generated successfully');
    }



}
