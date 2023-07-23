<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class ScheduleFindDayTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find:daytime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find schedule day and time';

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
        $scheduleinfo = DB::table('scheduled_smses')->where('status',false)->orderBy('submitted_at','asc')->first();

        $findday = date("l", strtotime($scheduleinfo->submitted_at));
        $findtime = date("H:i", strtotime($scheduleinfo->submitted_at));
        $findhour = date("H", strtotime($scheduleinfo->submitted_at));
        $findmin = date("i", strtotime($scheduleinfo->submitted_at));
        $finddaynum = (int)date("d", strtotime($scheduleinfo->submitted_at));
        $finddaynumofweek = date("w", strtotime($scheduleinfo->submitted_at));
        $findmonthnum = (int)date("m", strtotime($scheduleinfo->submitted_at));

        $dayofweek = date('w', strtotime(date("Y-m-d")));
        $result    = date('Y-m-d', strtotime(($finddaynum - $dayofweek).' day', strtotime($scheduleinfo->submitted_at)));

        $this->info(strtolower($findtime));
    }
}
