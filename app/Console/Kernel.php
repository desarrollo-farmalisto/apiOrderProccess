<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('sanctum:prune-expired --hours=24')->daily();

        $schedule->command('extraccion:clientes')->cron('0 11,21 * * *')->withoutOverlapping(120);
        $schedule->command('extraccion:direcciones-clientes')->cron('45 11,21 * * *')->withoutOverlapping(120);
        $schedule->command('extraccion:listas-precios')->dailyAt('05:00')->withoutOverlapping(120);
        $schedule->command('extraccion:ubicaciones')->dailyAt('00:10')->withoutOverlapping(60);
        $schedule->command('extraccion:inventario')->cron('0 1,17 * * *')->withoutOverlapping(180);
        $schedule->command('extraccion:inventario-kits')->cron('45 1,17 * * *')->withoutOverlapping(180);
        $schedule->command('extraccion:catalogo')->cron('30 2,18 * * *')->withoutOverlapping(180);
        $schedule->command('extraccion:kits')->cron('20 */2 * * *')->withoutOverlapping(90);
        $schedule->command('extraccion:promociones')->cron('50 * * * *')->withoutOverlapping(45);
        // $schedule->command('extraccion:usuarios')->everyFourHours();
        $schedule->command('extraccion:medicos')->dailyAt('00:25')->withoutOverlapping(60);
        $schedule->command('extraccion:detalles-inventarios')->cron('15 3,19 * * *')->withoutOverlapping(180);
        $schedule->command('extraccion:catalogo-abbott')->dailyAt('00:40')->withoutOverlapping(120);
        $schedule->command('extraccion:inventarioAxa')->cron('0 4,20 * * *')->withoutOverlapping(180);
        $schedule->command('Actualizacion_estado_ordenes')->everyThreeMinutes()->withoutOverlapping(10);
        $schedule->command('app:envio-ordenes')->weeklyOn(1, '8:15')->withoutOverlapping(120);
        $schedule->command('extraccion:clean-netsuite-logs')->monthlyOn(1, '06:15')->withoutOverlapping(120);
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
