<?php

namespace App\Http\Controllers;

use App\Core\ContactsAndGroups\ContactsAndGroups;
use App\Jobs\SetupContactInAGroupJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use DB;
use App\Contact;
use App\ContactGroup;

class ContactGroupController extends Controller
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
    }

    public function manageGroups()
    {
        return view('smsview.contactGroup.contactGroupList');
    }
    
    public function contactGroupsData(Request $request)
    {

        // storing  request (ie, get/post) global array to a variable  
        $requestData= $_REQUEST;

        // echo $requestData['start']."<br>";
        // echo $requestData['length']; die();


        $columns = array( 
        // datatable column index  => database column name
            0 => 'group_name', 
            1 => 'status',
            2 => 'total_contacts',
            3 => 'created_at',
        );

        //laravel implementation
        $userId = Auth::guard('web')->user()->id;
        
        $sql = "SELECT contact_groups.id, contact_groups.group_name, contact_groups.status, (SELECT COUNT(id) FROM contacts WHERE contacts.contact_group_id=contact_groups.id) total_contacts, contact_groups.created_at FROM contact_groups WHERE user_id=$userId ";


        $sql2 = "SELECT * FROM (". $sql .") result WHERE 1 ";

        $users = DB::select($sql2);

        $totalData = count($users);
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

        $rows = DB::select($sql2);
        
        $totalFiltered = count($rows); // when there is a search parameter then we have to modify total number filtered rows as per search result.
            
        $sql2.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

        $rows = DB::select($sql2);

        $data = array();
        foreach ($rows as $row) {
            $nestedData=array(); 

            $nestedData[] = $row->group_name;
            $nestedData[] = $row->total_contacts;
            $nestedData[] = $row->created_at;
            if ($row->status==1) {

                $nestedData[] = 'Active';
            } else {
                $nestedData[] = 'Disabled';

            }

            

            $nestedData[] = "<a href='".route('contactgroup-details',$row->id)."' class='btn btn-sm btn-info'><i class='icon icon-eye' aria-hidden='true'></i> View Contacts</a> <a href='".route('contactgroup-delete',$row->id)."' class='btn btn-sm btn-danger confirm' data-confirm-button='Yes I am'
            data-cancel-button='Whoops no'><i class='fa fa-trash'></i> Delete</a>";
            

            $data[] = $nestedData;
        }
/*
        <span><a href='".route('contactgroup-details',$row->id)."' class='btn btn-sm btn-pure btn-info senderidedtfrm'><i class='icon icon-eye' aria-hidden='true'></i> View Contacts</a></span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			              		 <span><a href='".route('contactgroup-delete',$row->id)."' class='btn btn-sm  btn-pure btn-danger' data-toggle='confirmation'
	                                              data-btn-ok-label='Disable' data-btn-ok-icon='glyphicon glyphicon-share-alt'
	                                              data-btn-ok-class='btn-success'
	                                              data-btn-cancel-label='No' data-btn-cancel-icon='glyphicon glyphicon-ban-circle'
	                                              data-btn-cancel-class='btn-danger'
	                                              data-title='Are you sure?' data-content='You can again enable this.'>
	                                        <i class='fa fa-trash'></i> Delete
	                                      </a>
			              		</span>
*/


        $json_data = array(
                    "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                    "recordsTotal"    => intval( $totalData ),  // total number of records
                    "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
                    "data"            => $data   // total data array
                    );

        echo json_encode($json_data);  // send data as json format

    }

    public function contactGroupDetails($id)
    {

        $contactGroup = ContactGroup::find($id);

        if (!$contactGroup) {
            return redirect()->route('manage-groups')->with(['errmsg' => 'Invalid Request!']);
        }

        if ($contactGroup->user_id!=Auth::guard('web')->user()->id) {
            return redirect()->route('manage-groups')->with(['errmsg' => 'Invalid Request!']);
        }

        $totalContacts = Contact::where('contact_group_id', $id)->count();

        $client = Auth::guard('web')->user();
        $api_token = $client->api_token;

        return view('smsview.contactGroup.contactGroupDetails', compact('contactGroup', 'totalContacts', 'api_token'));

    }

    public function contactGroupNumbers($id)
    {

        // storing  request (ie, get/post) global array to a variable  
        $requestData= $_REQUEST;

        // echo $requestData['start']."<br>";
        // echo $requestData['length']; die();


        $columns = array( 
        // datatable column index  => database column name
            0 => 'contact_number', 
            1 => 'contact_name',
            2 => 'email',
            3 => 'gender',
            4 => 'dob',
        );

        //laravel implementation
        $userId = Auth::guard('web')->user()->id;
        
        $sql = "SELECT contacts.contact_number, contacts.contact_name, contacts.email, contacts.gender, contacts.dob FROM contacts WHERE contact_group_id=$id ";


        $sql2 = "SELECT * FROM (". $sql .") result WHERE 1 ";

        $users = DB::select($sql2);

        $totalData = count($users);
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

        // getting records as per search parameters

        if( !empty($requestData['columns'][0]['search']['value']) ){  //code
            $sql2.=" AND contact_number LIKE '%".$requestData['columns'][0]['search']['value']."%' ";
        }
        if( !empty($requestData['columns'][1]['search']['value']) ){  //code
            $sql2.=" AND contact_name LIKE '%".$requestData['columns'][1]['search']['value']."%' ";
        }

        $rows = DB::select($sql2);
        
        $totalFiltered = count($rows); // when there is a search parameter then we have to modify total number filtered rows as per search result.
            
        $sql2.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

        $rows = DB::select($sql2);

        $data = array();
        foreach ($rows as $row) {
            $nestedData=array(); 

            $nestedData[] = $row->contact_number;
            $nestedData[] = $row->contact_name;
            $nestedData[] = $row->email;
            $nestedData[] = $row->gender;
            $nestedData[] = $row->dob;            

            $data[] = $nestedData;
        }



        $json_data = array(
                    "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                    "recordsTotal"    => intval( $totalData ),  // total number of records
                    "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
                    "data"            => $data   // total data array
                    );

        echo json_encode($json_data);  // send data as json format

    }

    public function updateGroup(Request $request)
    {
        return $this->contactgroup->updateGroup([
            'id' => $request->id,
            'group_name' => $request->group_name,
            'status' => $request->status
        ]);
    }

    public function deleteGroup($id)
    {

        $contactGroup = ContactGroup::where('id', $id)->where('user_id', Auth::guard('web')->user()->id)->first();

        if (!$contactGroup) {
            return redirect()->route('manage-groups')->with(['errmsg' => 'Invalid Request!']);
        }

        $contactGroup->delete();


        Contact::where('contact_group_id', $id)->delete();

        return redirect()->route('manage-groups')->with('msg','Contact Group Deleted Successfully!');
    }

    protected function prepareContracts(Request $request, $contacts)
    {
        
        $filename = $this->contactgroup->getFileName();

        $contactgroup = $this->contactgroup;
        
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
                'contact_group_id' => $request->contactgroup,
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
                            
                                $this->contactgroup->addContactGroup([
                                    'contactformtype' => $request->contactformtype,
                                    'user_id' => Auth::guard('web')->user()->id,
                                    'contact_group_id' => $request->contactgroup,
                                    'contact_name' => $request->contact_name,
                                    'contact_number' => $request->contact_number,
                                    'contact_file_address' => null,
                                    'email' => $request->email,
                                    'gender' => $request->gender,
                                    'dob' => $request->dob,
                                    'status' => 1
                                ]);

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
