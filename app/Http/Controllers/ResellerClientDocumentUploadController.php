<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Core\ClientDocuments\ClientDocumentsInterface;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\User;

class ResellerClientDocumentUploadController extends Controller
{
    
    /**
     * Client Document upload service
     *
     * @var App\Core\ClientDocuments\ClientDocumentsInterface
     */
    protected $document;

    public function __construct(ClientDocumentsInterface $document)
    {
        $this->middleware('auth:reseller,root');
        $this->document = $document;
    }

    public function uploadDocuments(Request $request)
    {
        
        $rootuserid = Auth::guard('reseller')->user()->id;

        $findUser = new UserResource(User::where('id', $request->userid)->first());
        
        if(@$findUser->documents[0]->isVerified == true) {
            return back()
                    ->with('verified',1)
                    ->with('msg','Document is verified');
        }

        //$validator = $request->validate([
            //'nationalid' => 'required|image|mimes:jpeg,jpg,png|max:200',
            //'application' => 'required|file|mimes:doc,docx,pdf|max:200',
            //'custppphoto' => 'required|image|mimes:jpeg,jpg,png|max:200',
            //'tradelicence' => 'required|file|mimes:jpeg,jpg,pdf,png|max:1024'
        //]);

        $this->document
            ->addNid($request)
            ->addApplication($request)
            ->addCustomPhoto($request)
            ->addTradeLicence($request)
            ->addUserDocuments($request);

        return back()->with('msg','User documents successfully uploaded');
    }

    
}
