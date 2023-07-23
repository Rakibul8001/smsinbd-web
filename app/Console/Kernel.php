<?php

namespace App\Console;

use App\ScheduleSms;
use Illuminate\Console\Scheduling\Schedule;
use DB;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ArchiveUserSendSms::class,
        Commands\PopulateArchiveCampaign::class,
        //Commands\SendScheduleSms::class,
        //Commands\BackupDatabase::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $scheduleinfo = DB::table('scheduled_smses')->where('status',false)->orderBy('submitted_at','asc')->first();

        if (ScheduleSms::where('status',false)->orderBy('submitted_at','asc')->exists())
        {

            $findday = date("l", strtotime($scheduleinfo->submitted_at));
            $findtime = date("H:i", strtotime($scheduleinfo->submitted_at));
            $findhour = date("H", strtotime($scheduleinfo->submitted_at));
            $findmin = date("i", strtotime($scheduleinfo->submitted_at));

            $finddaynum = (int)date("d", strtotime($scheduleinfo->submitted_at));

            $findmonthnum = (int)date("m", strtotime($scheduleinfo->submitted_at));

            $day = strtolower($findday."s");
            

            $schedule->command('schedule:smssend')->$day()->at("$findtime");
        }

        $schedule->command('archive:sentsms')->everyMinute();//->everyFifteenMinutes();//->dailyAt('00:00');
        //$schedule->command('archive:sentsms')->everyMinute();//->dailyAt('01:00');
        //$schedule->command('archive:sentsms')->everyMinute();//->dailyAt('02:00');
        //$schedule->command('archive:sentsms')->everyMinute();//->dailyAt('03:00');
        $schedule->command('archive:campaign')->dailyAt('02:00');//->hourly();//
        //$schedule->command('backup:database')->dailyAt('12:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
