<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

class RunScheduledExtraction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximo 20 minutos para extraccion.
     *
     */
    public $timeout = 1200;

    /**
     * No repetir extracciones.
     *
     */
    public $tries = 1;

    /**
     * Evitar que se ejecute al mismo tiemop el mismo comando.
     *
     */
    public $uniquefor = 1800;
    public string $command;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $command)
    {
        return $this->command = $command;
    }

    public function uniqueId(): string
    {
        return $this->command;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $exitCode = Artisan::call($this->command);
        if ($exitCode != 0) {
            throw new RuntimeException("El comando: {$this->command}. terminó con código: {$exitCode}");
        }
    }
}
