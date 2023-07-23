<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateArchiveCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive:campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currentdate = Carbon::today();//Carbon::now()->subDays(1)->toDateString();//Carbon::today();
    
        //DB::table('archive_campaign')->truncate();

        $archive = DB::select(DB::raw("
        insert into archive_campaign(name,email,userid,smsid,totalcampaign,send_from,sms_catagory,sms_type,sender_name,contact,sms_content,smscount,status,submitted_at)
        select   `u`.`name` AS `name`,`u`.`email` AS `email`,`us`.`user_id` AS `userid`,substr(`us`.`remarks`,10,length(`us`.`remarks`)) AS `smsid`,
                count(`us`.`remarks`) AS `totalcampaign`,`us`.`send_type` AS `send_from`,
                `us`.`sms_catagory` AS `sms_catagory`,`us`.`sms_type` AS `sms_type`,
                `s`.`sender_name` AS `sender_name`,count(`us`.`to_number`) AS `contact`,`us`.`sms_content` AS `sms_content`,
                sum(`us`.`number_of_sms`) AS `smscount`,`us`.`status` AS `status`,
                `us`.`submitted_at` AS `submitted_at` 
                from `archive_sent_smses` us
        join `users` `u` 
        on `u`.`id` = `us`.`user_id` 
        join `sms_senders` `s` 
        on `s`.`id` = `us`.`user_sender_id`
        where DATE(us.submitted_at) <'$currentdate'
        and substr(`us`.`remarks`,10,length(`us`.`remarks`)) NOT IN(select smsid from archive_campaign)
        group by `us`.`remarks`
        "));
    }
}
