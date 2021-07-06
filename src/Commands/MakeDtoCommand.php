<?php

namespace Emsifa\Evo\Commands;

use Illuminate\Console\Command;

class MakeDtoCommand extends Command
{
    public $signature = 'evo:make-dto';

    public $description = 'Generate DTO file';

    public function handle()
    {
        $this->comment('All done');
    }
}
