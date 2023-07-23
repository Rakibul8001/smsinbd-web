<?php

namespace App\Core\Senderid;

use DB;
use App\Core\Senderid\SenderId;
use App\Http\Resources\SmsSenderIdResource;
use App\SmsSender;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SenderidDetails implements SenderId
{

    /**
     * Add Sender Id
     *
     * @param array $data
     * @return void
     */
    public function addSenderId(array $data)
    {
        if (! is_array($data))
        {
            return response()->json(['errmsg'=>'Data must be an array']);
        }

        if ($data['sendertype'] == 'general') {

            if (isset($data['gateway_info']) && !empty($data['gateway_info']))
            {
                $senderid = new SmsSenderIdResource(SmsSender::create([
                    'sender_name' => $data['sender_name'],
                    'status' => $data['status'],
                    'default' => $data['default'],
                    'gateway_info' => $data['gateway_info'],
                    'created_by' => $data['created_by'],
                    'updated_by' => $data['updated_by']
                ]));
            }

            if (isset($data['rotation_gateway_info']) && !empty($data['rotation_gateway_info']))
            {
                $senderid = new SmsSenderIdResource(SmsSender::create([
                    'sender_name' => $data['sender_name'],
                    'status' => $data['status'],
                    'default' => $data['default'],
                    'rotation_gateway_info' => $data['rotation_gateway_info'],
                    'created_by' => $data['created_by'],
                    'updated_by' => $data['updated_by']
                ]));
            }

            if (isset($data['multiple_template_gateway_info']) && !empty($data['multiple_template_gateway_info']))
            {
                $senderid = new SmsSenderIdResource(SmsSender::create([
                    'sender_name' => $data['sender_name'],
                    'status' => $data['status'],
                    'default' => $data['default'],
                    'multiple_template_gateway_info' => $data['multiple_template_gateway_info'],
                    'created_by' => $data['created_by'],
                    'updated_by' => $data['updated_by']
                ]));
            }
        }

        if ($data['sendertype'] == 'teletalk') {
            $senderid = new SmsSenderIdResource(SmsSender::create([
                'sender_name' => $data['sender_name'],
                'operator_id' => $data['operator_id'],
                'status' => $data['status'],
                'default' => $data['default'],
                'user' => $data['user'],
                'password' => $data['password'],
                'created_by' => $data['created_by'],
                'updated_by' => $data['updated_by']
            ]));
        }

        return response()->json(['msg' => 'Sender ID successfully created']);
    }


    /**
     * Update Sender ID
     *
     * @param array $data
     * @return void
     */
    public function updateSenderId(array $data)
    {
        if (! is_array($data))
        {
            return response()->json(['errmsg'=>'Data must be an array']);
        }

        if ($data['sendertype'] == 'general') {
            if (isset($data['gateway_info']) && !empty($data['gateway_info']))
            {
                $senderid = new SmsSenderIdResource(SmsSender::where('id',$data['smssender_rec_id'])->update([
                    'sender_name' => $data['sender_name'],
                    'status' => $data['status'],
                    'default' => $data['default'],
                    'gateway_info' => $data['gateway_info'],
                    'created_by' => $data['created_by'],
                    'updated_by' => $data['updated_by']
                ]));
            }

            if (isset($data['rotation_gateway_info']) && !empty($data['rotation_gateway_info']))
            {
                $senderid = new SmsSenderIdResource(SmsSender::where('id',$data['smssender_rec_id'])->update([
                    'sender_name' => $data['sender_name'],
                    'status' => $data['status'],
                    'default' => $data['default'],
                    'rotation_gateway_info' => $data['rotation_gateway_info'],
                    'created_by' => $data['created_by'],
                    'updated_by' => $data['updated_by']
                ]));

                DB::table('runtime_rotations')->where('senderid',$data['sender_name'])->delete();
            }

            if (isset($data['multiple_template_gateway_info']) && !empty($data['multiple_template_gateway_info']))
            {
                $senderid = new SmsSenderIdResource(SmsSender::where('id',$data['smssender_rec_id'])->update([
                    'sender_name' => $data['sender_name'],
                    'status' => $data['status'],
                    'default' => $data['default'],
                    'multiple_template_gateway_info' => $data['multiple_template_gateway_info'],
                    'created_by' => $data['created_by'],
                    'updated_by' => $data['updated_by']
                ]));

                DB::table('template_runtime_rotations')->where('senderid',$data['sender_name'])->delete();
            }
        }

        if ($data['sendertype'] == 'teletalk') {
            $senderid = new SmsSenderIdResource(SmsSender::where('id',$data['smssender_rec_id'])->update([
                'sender_name' => $data['sender_name'],
                'operator_id' => $data['operator_id'],
                'status' => $data['status'],
                'default' => $data['default'],
                'user' => $data['user'],
                'password' => $data['password'],
                'created_by' => $data['created_by'],
                'updated_by' => $data['updated_by']
            ]));
        }

        return response()->json(['msg' => 'Sender ID successfully created']);
    }

    /**
     * Check the sender id exist in database
     *
     * @param int $id
     * @return void
     */
    public function isValidSenderId($id)
    {
        if(! isset($id) || empty($id)) 
        {
            return false;
        }

        if (SmsSender::where('id',$id)->exists())
        {
            return true;
        }

        return false;
    }


    /**
     * Delete sender ID
     *
     * @param int $id
     * @return void
     */
    public function deleteSenderId($id)
    {
        
    }

    /**
     * Show SMS Sender Ids
     *
     * @return void
     */
    public function showSmsSenderId($sendertype=null)
    {
        
        if ($sendertype == "teletalk")
        {
            $data = [];
            $senderids = SmsSenderIdResource::collection(SmsSender::where('user','!=',NULL)->where('password','!=',NULL)->get());
            
            foreach($senderids as $senderid) {

                    $data['data'][] = [
                    'id' => $senderid->id,
                    'sender_name' => $senderid->sender_name,
                    'operator_id' => $senderid->operator_id,
                    'operator_name' => $senderid->operator,
                    'status' => $senderid->status,
                    'default' => $senderid->default,
                    'user' => $senderid->user,
                    'password' => $senderid->password,
                    'gateway_info' => $senderid->gateway_info,
                    'rotation_gateway_info' => $senderid->rotation_gateway_info,
                    'multiple_template_gateway_info' => $senderid->multiple_template_gateway_info,
                    'created_by' => $senderid->createdBy->name,
                    'updated_by' => $senderid->updatedBy->name
                        
                    ];
            }

            return $data;
        }

        $data = [];
        $senderids = SmsSenderIdResource::collection(SmsSender::where('user','=',NULL)->where('password','=',NULL)->get());
        
        foreach($senderids as $senderid) {

                $data['data'][] = [
                   'id' => $senderid->id,
                   'sender_name' => $senderid->sender_name,
                   'operator_id' => $senderid->operator_id,
                   'operator_name' => $senderid->operator,
                   'status' => $senderid->status,
                   'default' => $senderid->default,
                   'user' => $senderid->user,
                   'password' => $senderid->password,
                   'gateway_info' => $senderid->gateway_info,
                   'rotation_gateway_info' => $senderid->rotation_gateway_info,
                   'multiple_template_gateway_info' => $senderid->multiple_template_gateway_info,
                   'created_by' => $senderid->createdBy->name,
                   'updated_by' => $senderid->updatedBy->name
                    
                ];
        }

        return $data;
    }


    /**
     * Show Reseller SMS Sender Ids
     *
     * @return void
     */
    public function showResellerSmsSenderId($resellerid = null)
    {
        

        $data = [];
        $resellerid = !empty(Auth::guard('reseller')->user()) ? Auth::guard('reseller')->user()->id : $resellerid;
        
        $senderids = SmsSenderIdResource::collection(SmsSender::whereIn('id', function($query) use($resellerid){
            $query->select('sms_sender_id')
                  ->from('reseller_senders')
                  ->where('reseller_id',$resellerid);
        })->get());
        
        foreach($senderids as $senderid) {

                $data['data'][] = [
                   'id' => $senderid->id,
                   'sender_name' => $senderid->sender_name,
                   'operator_id' => $senderid->operator_id,
                   'operator_name' => $senderid->operator,
                   'status' => $senderid->status,
                   'default' => $senderid->default,
                   'user' => $senderid->user,
                   'password' => $senderid->password,
                   'gateway_info' => $senderid->gateway_info,
                   'created_by' => $senderid->createdBy->name,
                   'updated_by' => $senderid->updatedBy->name
                    
                ];
        }

        return $data;
    }


    public function getSenderIdById($senderid)
    {
        if (! isset($senderid) || empty($senderid))
        {
            return response()->json(['errmsg' => 'Senderid missing'], 406);
        }

        return new SmsSenderIdResource(SmsSender::where('id',$senderid)->first());
    }

    public function getSenderIdByName($senderid)
    {
        if (! isset($senderid) || empty($senderid))
        {
            return response()->json(['errmsg' => 'Senderid missing'], 406);
        }

        if (SmsSender::where('sender_name',$senderid)->where('gateway_info','!=',NULL)->exists())
        {
            return new SmsSenderIdResource(SmsSender::where('sender_name',$senderid)->where('gateway_info','!=',NULL)->first());
        } 
        
        if (SmsSender::where('sender_name',$senderid)->where('gateway_info','=',NULL)->exists())
        {
            return new SmsSenderIdResource(SmsSender::where('sender_name',$senderid)->where('gateway_info','=',NULL)->first());
        }

        return response()->json(['errmsg' => 'Sender Id Not Found'], 406);
    }

    public function getTeletalkSenderIdByName($senderid)
    {
        if (! isset($senderid) || empty($senderid))
        {
            return response()->json(['errmsg' => 'Senderid missing'], 406);
        }

        if (SmsSender::where('sender_name',$senderid)->where('gateway_info','=',NULL)->exists())
        {
            return new SmsSenderIdResource(SmsSender::where('sender_name',$senderid)->where('gateway_info','=',NULL)->first());
        }
    }
}