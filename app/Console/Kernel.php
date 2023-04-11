<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

  /**
   * The Artisan commands provided by your application.
   *
   * @var array
   */
  protected $commands = [
      Commands\CheckPartee::class,
      Commands\CreateMonthLimpieza::class,
      Commands\SendSecondPay::class,
      Commands\SendParteeAdmin::class,
      Commands\SendParteeReminder::class,
      Commands\CreatePaymentFianza::class,
      Commands\ProcessData::class,
      Commands\PricesSeason::class,
      Commands\MinStaySeason::class,
      Commands\SendPoll::class,
      Commands\SafeBox::class,
      Commands\ProcessMultipleRoomLock::class,
      Commands\SendAvailibilityMonth::class,
      Commands\SafeBoxUnassing::class,
      Commands\ScheduleList::class,
      Commands\ClearSessions::class,
      Commands\BookingsDays::class,
      Commands\CheckBookings::class,
      Commands\ProcessPaylandSeasson::class,
      Commands\CheckBookingsCheckin::class,
      Commands\Testing::class,
//      Commands\SendSupplements::class,
      Commands\CheckPricess::class,
      Commands\SendCheckinMsg::class,
      Commands\CreateMonthAgency::class,
      Commands\CheckOtaService::class,
      Commands\WubookAvailables::class,
      Commands\WubookRates::class,
      Commands\WubookMinStay::class,
      Commands\WubookBooks::class,
      Commands\WubookGetAllBooking::class,
      Commands\CheckOtaRrvs::class,
      Commands\CancelCustomerBlockBooks::class,
  ];

  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
   * @return void
   */
  protected function schedule(Schedule $schedule) {
    $schedule->command('monthAgency:create')->monthly()->timezone('Europe/Madrid');
    //Le quito 2 horas de diff
//      $schedule->command('SafeBoxUnassing:unasign')->dailyAt('6:00');
    $schedule->command('partee:sendReminder')->dailyAt('7:00')->timezone('Europe/Madrid');
    $schedule->command('secondPay:sendEmails')->dailyAt('7:00')->timezone('Europe/Madrid');
//    $schedule->command('Supplements:sendBuy')->dailyAt('7:00')->timezone('Europe/Madrid');
    $schedule->command('SafeBox:asignAndSend')->dailyAt('14:30')->timezone('Europe/Madrid');
    $schedule->command('SendPoll:sendEmails')->dailyAt('12:00')->timezone('Europe/Madrid');

    $schedule->command('partee:sendAlert')->dailyAt('21:00')->timezone('Europe/Madrid');
    $schedule->command('ClearSessions:process')->dailyAt('4:00')->timezone('Europe/Madrid');
    $schedule->command('OTAs:checkBookings')->dailyAt('1:00')->timezone('Europe/Madrid');
    $schedule->command('PaylandSeasson:process')->dailyAt('4:00')->timezone('Europe/Madrid');
    $schedule->command('OTAs:checkBookingsCheckin')->dailyAt('1:40')->timezone('Europe/Madrid');

    $schedule->command('MultipleRoomLock:Process')->hourly();
    $schedule->command('BookingsDays:load')->hourly();
    $schedule->command('OTAs:CheckOtaRrvs')->everyMinute();

    $schedule->command('partee:check')->everyThirtyMinutes();
    $schedule->command('OTAs:CheckPricess')->everyThirtyMinutes();
//         $schedule->command('zodomus:importAll')->everyThirtyMinutes();
    $schedule->command('ProcessData:all')->everyFiveMinutes();


    $schedule->command('wubook:sendRates')->everyTenMinutes();
    $schedule->command('wubook:sendMinStay')->everyTenMinutes();
    $schedule->command('wubook:sendAvaliables')->everyFiveMinutes();
    $schedule->command('wubook:WubookBooks')->everyMinute();
    $schedule->command('wubook:getAllBookings')->hourly();

//         $schedule->command('wubook:getBookings')->everyMinute();
    $schedule->command('OTAs:sendMinStaySeason')->everyFiveMinutes();
    $schedule->command('OTAs:sendPricesSeason')->everyMinute();

    //everyThreeHours
    $schedule->command('OTAs:SendAvailibilityMonth')->dailyAt('4:00')->timezone('Europe/Madrid');
    $schedule->command('OTAs:SendAvailibilityMonth')->dailyAt('7:00')->timezone('Europe/Madrid');
    $schedule->command('OTAs:SendAvailibilityMonth')->dailyAt('11:00')->timezone('Europe/Madrid');
    $schedule->command('OTAs:SendAvailibilityMonth')->dailyAt('15:00')->timezone('Europe/Madrid');
    $schedule->command('OTAs:SendAvailibilityMonth')->dailyAt('19:00')->timezone('Europe/Madrid');
    $schedule->command('OTAs:SendAvailibilityMonth')->dailyAt('22:00')->timezone('Europe/Madrid');

     //everyEightHours
    $schedule->command('OTAs:CheckService')->dailyAt('6:00')->timezone('Europe/Madrid');
    $schedule->command('OTAs:CheckService')->dailyAt('14:00')->timezone('Europe/Madrid');
    $schedule->command('OTAs:CheckService')->dailyAt('22:00')->timezone('Europe/Madrid');
    $schedule->command('CancelCustomerBlocks:sendExpiredPayment')->dailyAt('00:50')->timezone('Europe/Madrid');
  }

}
