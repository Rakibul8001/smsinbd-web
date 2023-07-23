<?php

namespace App\Jobs;

use App\Core\ContactsAndGroups\ContactsAndGroupsDetails;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetupContactInAGroupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;


    protected $data = [];
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contactgroup = new ContactsAndGroupsDetails();

        foreach($this->data['contact_number'] as $contact)
        {

        
            $contactgroup->addContactGroup([
                'contactformtype' => $this->data['contactformtype'],
                'user_id' => $this->data['user_id'],
                'contact_group_id' => $this->data['contact_group_id'],
                'contact_name' => $this->data['contact_name'],
                'contact_number' => $contact,
                'contact_file_address' => $this->data['contact_file_address'],
                'email' => $this->data['email'],
                'gender' => $this->data['gender'],
                'dob' => $this->data['dob'],
                'status' => $this->data['status']
            ]);

        }
    }
}
