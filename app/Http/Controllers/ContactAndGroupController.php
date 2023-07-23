<?php

namespace App\Http\Controllers;

use App\Core\ContactsAndGroups\ContactsAndGroups;
use App\Jobs\SetupContactInAGroupJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContactAndGroupController extends Controller
{
    /**
     * Contact group service
     *
     * @var App\Core\ContactsAndGroups\ContactsAndGroupsDetails
     */
    protected $contactgroup;

    public function __construct(ContactsAndGroups $contactgroup)
    {
        $this->middleware('auth:web,root');

        $this->contactgroup = $contactgroup;

        ini_set('max_execution_time', 0); 
    }

    public function createGroup(Request $request)
    {
        $frmmode = ['ins','edt'];

        if (in_array($request->frmmode, $frmmode)) {

            switch($request->frmmode)
            {
                case 'ins':
                    return $this->contactgroup->createGroup([
                        'user_id' => Auth::guard('web')->user()->id,
                        'group_name' => $request->group_name,
                        'status' => $request->status,
                        'id' => $request->id,
                        'frmmode' => $request->frmmode
                    ]);
                    break;
                case 'edt':
                    return $this->contactgroup->updateGroup([
                        'group_name' => $request->group_name,
                        'status' => $request->status,
                        'id' => $request->id,
                    ]);
                    break;
                default:
                    return response()->json(['msg' => 'Form submission mode not defined'], 406);

            }

        }

        return response()->json(['msg' => 'Form submission mode not defined'], 406);
    }

    public function showGroups()
    {
        return view('smsview.contactandgroup.grouplist');
    }
    
    public function renderGroups()
    {
        return $this->contactgroup->showGroups();

    }

    public function getGroupById($groupid)
    {
        return $this->contactgroup->getGroupById($groupid);
    }

    public function updateGroup(Request $request)
    {
        return $this->contactgroup->updateGroup([
            'id' => $request->id,
            'group_name' => $request->group_name,
            'status' => $request->status
        ]);
    }

    public function deleteGroup(Request $request)
    {
        if (! $request->has('id'))
        {
            return response()->json(['errmsg' => 'Record Id Not Found'], 406);
        }

        return $this->contactgroup->deleteGroup($request->id);
    }

    protected function prepareContracts(Request $request, $contacts)
    {
        
        $filename = $this->contactgroup->getFileName();

        $contactgroup = $this->contactgroup;
        
        foreach($request->contactgroup as $group)
        {
           // $queuename = 'contacts'.Auth::guard('web')->user()->id.mt_rand(1,100);
            /*dispatch(new SetupContactInAGroupJob([
                'contactformtype' => $request->contactformtype,
                'user_id' => Auth::guard('web')->user()->id,
                'contact_group_id' => $group,
                'contact_name' => $request->contact_name,
                'contact_number' => $contacts,//$request->contact_number,
                'contact_file_address' => $filename,
                'email' => $request->email,
                'gender' => $request->gender,
                'dob' => $request->dob,
                'status' => 1
            ]))->delay(now()->addSeconds(1));
            */
            foreach($contacts as $contact)
            {

            
                $this->contactgroup->addContactGroup([
                    'contactformtype' => $request->contactformtype,
                    'user_id' => Auth::guard('web')->user()->id,
                    'contact_group_id' => $group,
                    'contact_name' => $request->contact_name,
                    'contact_number' => $contact,
                    'contact_file_address' => $filename,
                    'email' => $request->email,
                    'gender' => $request->gender,
                    'dob' => $request->dob,
                    'status' => 1
                ]);

            }
        }

    }

    public function addContactGroup(Request $request)
    {
        $contactformtype = ['single','multiple'];
        $formtype = ['ins','edt'];

        if (in_array($request->frmmode, $formtype))
        {
            switch($request->frmmode)
            {
                case 'ins':
                    if (in_array($request->contactformtype, $contactformtype))
                    {
                        if ($request->contactformtype == 'single')
                        {
                            foreach($request->contactgroup as $group)
                            {
                                $this->contactgroup->addContactGroup([
                                    'contactformtype' => $request->contactformtype,
                                    'user_id' => Auth::guard('web')->user()->id,
                                    'contact_group_id' => $group,
                                    'contact_name' => $request->contact_name,
                                    'contact_number' => $request->contact_number,
                                    'contact_file_address' => null,
                                    'email' => $request->email,
                                    'gender' => $request->gender,
                                    'dob' => $request->dob,
                                    'status' => 1
                                ]);
                            }

                            return response()->json(['msg' => 'Contact inserted successfully'], 200);
                        }

                        if ($request->contactformtype == 'multiple')
                        {

                            $filename = $this->contactgroup->addContactFile($request);

                            $extension = $this->contactgroup->getFileExtension();
                            if ($extension === 'csv')
                            {
                                $contacts = $this->contactgroup->getBdMobileNumberFromCSV();
                                try{
                                    $this->prepareContracts($request, $contacts);
                                } catch(\Exception $e) {
                                    return response()->json(['errmsg' => $e->getMessage()]);
                                }
                                

                                //Artisan::call('queue:work');

                                return response()->json(['msg' => 'Multiple Contacts from csv file inserted successfully'], 200);

                            } else if($extension === 'xls' || $extension === 'xlsx') {

                                $contacts = $this->contactgroup->getBDMobileNumberFromXlsOrXlsx();

                                try{
                                    $this->prepareContracts($request, $contacts);
                                } catch(\Exception $e) {
                                    return response()->json(['errmsg' => $e->getMessage()]);
                                }

                                //Artisan::call('queue:work');

                                return response()->json(['msg' => 'Multiple Contacts from xls file inserted successfully'], 200);

                            } else if ($extension === 'txt') {
                                
                                $contacts = $this->contactgroup->getBDMobileNumberFromTextFile();

                                try{
                                    $this->prepareContracts($request, $contacts);
                                } catch(\Exception $e) {
                                    return response()->json(['errmsg' => $e->getMessage()]);
                                }

                                //Artisan::call('queue:work');

                                return response()->json(['msg' => 'Multiple Contacts from text file inserted successfully'], 200);

                            } else {
                                return response()->json(['msg' => 'There is an error, problem may be invalid file format!'], 406);
                            }

                            return response()->json(['msg' => 'There is an error, problem may be invalid file format!'], 406);
                        }
                    }
                case 'edt':
                break;
                default:
                    return false;
            }
            
        }


        /*return $request;
        return implode(",", $request->contactgroup);
        $this->addContactFile($request->file);
        foreach($request->contactgroup as $group)
        {
            return $group;
        }
        return $this->contactgroup->addContactGroup([
            'user_id' => $request->user_id,
            'contact_group_id' => $request->contact_group_id,
            'contact_name' => $request->contact_name,
            'contact_number' => $request->contact_number,
            'contact_file_address' => $request->contact_file_address,
            'email' => $request->email,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'status' => $request->status
        ]);
        */
    }

    public function showContactGroups()
    {
        $groups = $this->contactgroup->getGroupsByClient(Auth::guard('web')->user()->id);
        return view('smsview.contactandgroup.contacts',compact('groups'));
    }

    public function renderContactsInGroup()
    {
        return $this->contactgroup->showContactGroups();
    }

    public function getContactGroupById($contact_group_id)
    {
        return $this->contactgroup->getContactGroupById($contact_group_id);
    }

    public function updateContactGroup(Request $request)
    {
        return $this->contactgroup->updateContactGroup([
            'id' => $request->id,
            'contact_group_id' => $request->contact_group_id,
            'contact_name' => $request->contact_name,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'status' => $request->status
        ]);
    }

    public function deleteContactGroup($contactid)
    {
        return $this->contactgroup->deleteContactGroup($contactid);
    }
}
