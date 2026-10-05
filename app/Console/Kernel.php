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

        $schedule->command('extraccion:clientes')->cron('0 12,21 * * *');
        $schedule->command('extraccion:direcciones-clientes')->cron('0 11:30,21:30 * * *');
        $schedule->command('extraccion:listas-precios')->cron('0 4 * * *');
        $schedule->command('extraccion:ubicaciones')->daily();
        $schedule->command('extraccion:inventario')->cron('0 1,18 * * *');
        $schedule->command('extraccion:inventario-kits')->cron('0 1:30,18:30 * * *');
        $schedule->command('extraccion:catalogo')->cron('0 2,19 * * *');
        $schedule->command('extraccion:kits')->everyTwoHours();
        $schedule->command('extraccion:promociones')->hourly();
        // $schedule->command('extraccion:usuarios')->everyFourHours();
        $schedule->command('extraccion:medicos')->daily();
        $schedule->command('extraccion:detalles-inventarios')->cron('0 2,19 * * *');
        $schedule->command('extraccion:catalogo-abbott')->daily();
        $schedule->command('extraccion:inventarioAxa')->cron('0 3,20 * * *');
        $schedule->command('Actualizacion_estado_ordenes')->cron('*/3 * * * *');
        $schedule->command('app:envio-ordenes')->weeklyOn(1, '8:00');
        $schedule->command('extraccion:clean-netsuite-logs')->monthlyOn(1, '06:00');
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
