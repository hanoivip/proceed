<?php

namespace Hanoivip\Proceed\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\Proceed\Services\ProceedService;

class ProceedPersist extends Command
{
    protected $signature = 'proceed:persist';

    protected $description = 'Persist proceed points';
    
    private $proceed;

    public function __construct(ProceedService $proceed)
    {
        parent::__construct();
        
        $this->proceed = $proceed;
    }

    public function handle()
    {
        try
        {
            $this->proceed->persist();
            $this->info("ok");
        }
        catch (Exception $ex)
        {
            Log::error("Proceed persist exception: " . $ex->getMessage());
            $this->error($ex->getMessage());
        }
    }
}
