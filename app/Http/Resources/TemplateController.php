<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Core\Templates\Template;
use App\Http\Resources\TemplateResource;
use App\Template as AppTemplate;
use App\Core\HandleFile\HandleFile;

class TemplateController extends Controller
{
    protected $template;

    protected $handlefile;

    protected $extension;

    protected $file;

    public function __construct(
        Template $template,
        HandleFile $handlefile
    )
    {
        $this->template = $template;

        $this->handlefile = $handlefile;
    }

    public function manageTemplate()
    {
        return view('smsview.template.manage-template');
    }

    public function clientTemplate()
    {
        return view('smsview.template.client-template');
    }

    public function manageClientTemplate(Request $request)
    {
        return $this->template->clientTemplate([
            'userid' => Auth::guard('web')->user()->id,
            'usertype' => 'client'
        ]);
    }

    public function manageRootTemplate(Request $request)
    {
        return $this->template->rootTemplate();
    }

    public function approvedTemplteUploadedFile(Request $request) {
        
        $this->file = $this->handlefile->addFile($request);

        $extension = $this->handlefile->getFileExtension();

        $dataarr = [];
        if ( $extension == 'xls' ||  $extension == 'xlsx')
        {
            $contents = $this->handlefile->readXlsFile();

            foreach($contents as $key => $content) {
                foreach($content as $data) {
                    
                    $dataarr[] = $data;
                    
                }
            }

            $x = 0;

            if (preg_match_all("/{(.*?)}/", $request->template_desc, $m)) {
                
                if (count($dataarr) !== count($m[1])) {

                    return response()->json(['errmsg' => 'Template content mitch match, with template format'], 406);

                }

                foreach ($m[1] as $i => $varname) {
                    if ($x == 0) {
                        $this->template = str_replace($m[0][$i], $dataarr[$i], $request->template_desc);
                        $x = 1;
                    } else {
                        $this->template = str_replace($m[0][$i], $dataarr[$i], $this->template);
                    }
                    
                }
            }

        }
    }

    public function saveTemplate(Request $request)
    {
        if ($request->frmmode == 'ins')
        {

        
            if (Auth::guard('root')->check())
            {
                $user_id = Auth::guard('root')->user()->id;
                $usertype = 'root';
            } else {
                $user_id = Auth::guard('web')->user()->id;
                $usertype = 'client';
            }

            if (Auth::guard('web')->check())
            {
                $this->approvedTemplteUploadedFile($request);

                $this->template->addTemplate([
                    'template_title' => $request->template_title,
                    'template_desc' => $this->template,//$request->template_desc,
                    'content_file' => $this->file,
                    'user_id' => $user_id,
                    'user_type' => $usertype,
                    'status' => false,
                    'frmmode' => $request->frmmode,
                    'id' => $request->id
                ]);

                return back()->with('msg','Template inserted successfully');
            }

            $this->template->addTemplate([
                'template_title' => $request->template_title,
                'template_desc' => $request->template_desc,
                'user_id' => $user_id,
                'user_type' => $usertype,
                'status' => $request->status,
                'frmmode' => $request->frmmode,
                'id' => $request->id
            ]);

            return back()->with('msg','Template inserted successfully');
        }


        if ($request->frmmode == 'edt')
        {
            if (AppTemplate::where('id', $request->id)->exists())
            {

                $template = new TemplateResource(AppTemplate::where('id', $request->id)->first());
                if ($template->user_type == 'root') {
                    $this->template->addTemplate([
                        'template_title' => $request->template_title,
                        'template_desc' => $request->template_desc,
                        'user_id' => $template->user_id,
                        'user_type' => $template->user_type,
                        'status' => $request->status,
                        'frmmode' => $request->frmmode,
                        'id' => $request->id
                    ]);

                    return back()->with('msg','Template updated successfully');
                }

                if ($template->user_type == 'client') {

                    $this->approvedTemplteUploadedFile($request);

                    $this->template->addTemplate([
                        'template_title' => $request->template_title,
                        'template_desc' => $this->template,//$request->template_desc,
                        'content_file' => $this->file,
                        'user_id' => $template->user_id,
                        'user_type' => $template->user_type,
                        'status' => $request->status,
                        'frmmode' => $request->frmmode,
                        'id' => $request->id
                    ]);

                    return back()->with('msg','Template updated successfully');
                }
            }
        }
    }
}
