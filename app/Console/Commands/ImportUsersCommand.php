<?php

namespace App\Console\Commands;

use App\Components\ImportUsersClient;
use App\Models\Address;
use App\Models\Company;
use App\Models\User;
use App\Services\ImportUsersService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importUsers:everyMinutes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get users';

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
     * @return int
     */
    public function handle()
    {
        new ImportUsersService();
        return 0;
    }

}
