<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Core\ResellerDocuments\ResellerDocumentsInterface;
use App\Http\Resources\ResellerResource;
use Illuminate\Support\Facades\Auth;
use App\Reseller;

class RootResellerDocumentUploadController extends Controller
{
    
    /**
     * Client Document upload service
     *
     * @var App\Core\ClientDocuments\ClientDocumentsInterface
     */
    protected $document;

    public function __construct(ResellerDocumentsInterface $document)
    {
        $this->middleware('auth:root,manager');
        $this->document = $document;
    }

    public function uploadDocuments(Request $request)
    {
        
        $rootuserid = @Auth::guard('root')->user()->id;

        $findUser = new ResellerResource(Reseller::where('id', $request->userid)->first());
        
        if(@$findUser->documents[0]->isVerified == true) {
            return back()
                    ->with('verified',1)
                    ->with('msg','Document is verified');
        }

        /*$validator = $request->validate([
            'nationalid' => 'required|image|mimes:jpeg,jpg,png|max:200',
            'application' => 'required|file|mimes:doc,docx,pdf|max:200',
            'custppphoto' => 'required|image|mimes:jpeg,jpg,png|max:200',
            'tradelicence' => 'required|file|mimes:jpeg,jpg,pdf,png|max:1024'
        ]);*/

        $this->document
            ->addNid($request)
            ->addApplication($request)
            ->addCustomPhoto($request)
            ->addTradeLicence($request)
            ->addUserDocuments($request);

        return back()->with('msg','User documents successfully uploaded');
    }

    /**
     * Document Verified
     * 
     * @param Request $request
     * @return void
     */
    public function verifyDocument(Request $request)
    {
        return $this->document->verifyDocument($request);
    }


    public function changeClientStatus(Request $request)
    {
        return $this->document->changeClientStatus($request);
    }
}
