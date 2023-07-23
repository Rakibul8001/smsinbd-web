<?php

namespace App\Jobs;

use App\Http\Resources\UserSentSmsResource;
use App\UserSentSms;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserSmsSetupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data = [];

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;
    
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
        foreach($this->data['contacts'] as $contact) {
            new UserSentSmsResource(UserSentSms::create([
                'remarks' => $this->data['remarks'],
                'user_id' => $this->data['user_id'],
                'user_sender_id' => $this->data['user_sender_id'],
                'to_number' => $contact,
                'sms_type' => $this->data['sms_type'],
                'sms_catagory' => $this->data['sms_catagory'],
                'sms_content' => $this->data['sms_content'],
                'number_of_sms' => $this->data['number_of_sms'],
                'total_contacts' => $this->data['total_contacts'],
                'send_type' => $this->data['send_type'],
                'contact_group_id' => $this->data['contact_group_id'],
                'status' => $this->data['status'],
                'submitted_at' => $this->data['submitted_at'],
            ]));
        }
    }
}
