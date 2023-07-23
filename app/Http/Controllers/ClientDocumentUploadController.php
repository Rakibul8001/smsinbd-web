<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Core\ClientDocuments\ClientDocumentsInterface;
use Illuminate\Support\Facades\Auth;

class ClientDocumentUploadController extends Controller
{
    
    /**
     * Client Document upload service
     *
     * @var App\Core\ClientDocuments\ClientDocumentsInterface
     */
    protected $document;

    public function __construct(ClientDocumentsInterface $document)
    {
        $this->middleware('auth:web');
        $this->document = $document;
    }

    public function uploadDocuments(Request $request)
    {
        if(@Auth::guard('web')->user()->documents[0]->isVerified == true) {
            return back()
                    ->with('verified',1)
                    ->with('msg','Document is verified');
        }
        //$validator = $request->validate([
            //'nationalid' => 'required|image|mimes:jpeg,jpg,png|max:200',
            //'application' => 'required|file|mimes:doc,docx,pdf|max:200',
            //'custppphoto' => 'required|image|mimes:jpeg,jpg,png|max:200',
            //'tradelicence' => 'required|file|mimes:jpeg,jpg,pdf|max:500'

        //]);

        $clientDocuments = $this->document
                                ->showUserDocuments($request);

        
        
        $this->document
            ->addNid($request)
            ->addApplication($request)
            ->addCustomPhoto($request)
            ->addTradeLicence($request)
            ->addUserDocuments($request);
            

        return back()->with('msg','User documents successfully uploaded');
    }
}
