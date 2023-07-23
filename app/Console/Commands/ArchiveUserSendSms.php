<?php

namespace App\Console\Commands;

use App\ArchiveSentSms;
use App\User;
use App\UserSentSms;
use App\UserSentSmsBackup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArchiveUserSendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive:sentsms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive user sent smses to archive_sent_smses table';

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
        ini_set('memory_limit', '264M');
        
        $currentdate = Carbon::now()->subDays(1)->toDateString();//Carbon::today();

        $checksmssenttable = UserSentSms::whereDate('submitted_at','<',Carbon::now()->toDateString())->get();

        if (! $checksmssenttable->isEmpty())
        {
            DB::table("backups_stage")->update([
                'status' => true
            ]);
        }

        $backupstage = DB::table("backups_stage")->get();

        if ($backupstage[0]->status) {

            UserSentSms::query()
            ->whereDate('submitted_at','<',Carbon::now()->toDateString())
            ->each(function ($oldRecord) {
                $newRecord = $oldRecord->replicate();
                $newRecord->setTable('archive_sent_smses');
                $newRecord->save();

                $oldRecord->delete();
            });

            UserSentSms::query()
            ->whereDate('submitted_at',Carbon::now()->toDateString())
            ->each(function ($oldRecord) {
                $newRecord = $oldRecord->replicate();
                $newRecord->setTable('user_sent_smses_backup');
                $newRecord->save();

                $oldRecord->delete();
            });

            /*$balusers = User::whereIn('id', function($query){
                $query->select('user_id')
                ->from('archive_sent_smses');
            })->get();
            
            foreach($balusers as $user) {
                $smsbalance = DB::select(DB::raw("SELECT count(*),(select sum(number_of_sms) 
                                                        from archive_sent_smses 
                                                        where sms_catagory='mask'
                                                        and status = true
                                                        and user_id = $user->id) as 'mask',

                                                (select sum(number_of_sms) 
                                                        from archive_sent_smses 
                                                        where sms_catagory='nomask'
                                                        and status = true
                                                        and user_id = $user->id) as 'nonmask',

                                                (select sum(number_of_sms) 
                                                        from archive_sent_smses 
                                                        where sms_catagory='voice'
                                                        and status = true
                                                        and user_id = $user->id) as 'voice',
                                                DATE(submitted_at) balance_date

                                                FROM `archive_sent_smses`
                                                where status = true
                                                and user_id = $user->id
                            "));

                $checkbalance = DB::table("user_balance")->where('userid',$user->id)->get();

                if ($checkbalance->isEmpty()) { 
                    DB::table("user_balance")->insert([
                        'userid' => $user->id,
                        'mask' => !empty($smsbalance[0]->mask) ? $smsbalance[0]->mask : 0,
                        'nonmask' => !empty($smsbalance[0]->nonmask) ? $smsbalance[0]->nonmask : 0,
                        'voice' => !empty($smsbalance[0]->voice) ? $smsbalance[0]->voice : 0,
                        'balance_date' => $currentdate
                    ]);
                } else {
                    
                    DB::table("user_balance")->where('userid',$user->id)->update([
                        'mask' => !empty($smsbalance[0]->mask) ? $smsbalance[0]->mask : 0,
                        'nonmask' => !empty($smsbalance[0]->nonmask) ? $smsbalance[0]->nonmask : 0,
                        'voice' => !empty($smsbalance[0]->voice) ? $smsbalance[0]->voice : 0,
                        'balance_date' => $currentdate
                    ]);
                }
            }*/
        }

        $checksms = UserSentSms::all();

        if ($checksms->isEmpty()) {

                DB::table("backups_stage")->update([
                    'status' => false
                ]);

                DB::table("user_sent_smses")->truncate();
        } else {
            UserSentSmsBackup::query()
            ->whereDate('submitted_at',Carbon::today())
            ->each(function ($oldRecord) {
                $newRecord = $oldRecord->replicate();
                $newRecord->setTable('user_sent_smses');
                $newRecord->save();

                $oldRecord->delete();
            });

            $checksms = UserSentSmsBackup::all();
            if ($checksms->isEmpty()) {
                DB::table("user_sent_smses_backup")->truncate();
            }
        }

    }
}
