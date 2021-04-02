<?php

namespace Thomascombe\BackpackAsyncExport\Commands;

use Illuminate\Console\Command;

class BackpackAsyncExportCommand extends Command
{
    public $signature = 'backpack_async_export';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
