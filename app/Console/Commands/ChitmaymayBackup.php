<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChitmaymayBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chitmaymay:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'chitmaymay backup at 09:00 am';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $chitmaymay = "sudo rsync -aAXv --update --delete --ignore-errors /media/dkmads-upload/ /media/dkmads-upload-bk/";
        $chitmaymay2 = "sudo rsync -aAXv --update --delete --ignore-errors /media/dkmads-upload2/ /media/dkmads-upload2-bk/";
        $output = null;
        $returnVar = null;
        exec($chitmaymay,$output,$returnVar);
        exec($chitmaymay2);
        return Command::SUCCESS;
    }
}
