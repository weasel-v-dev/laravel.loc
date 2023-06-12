<?php

namespace App\Console\Commands;

use App\Components\ImportUsersClient;
use App\Models\Address;
use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        echo "Start import\n";
        $import = new ImportUsersClient();

        $response = $import->client->request('GET', 'users');
        $usersAPI = json_decode($response->getBody()->getContents(), true);

        echo "Importing...\n";
        $userAPIIds = [];

        foreach ($usersAPI as $i => $userAPI) {
             $userFound = User::find($userAPI['id']);
             $userAPIIds[] = $userAPI['id'];
             if($userFound) {
                 $userFound->update([
                     'name' => $userAPI['name'],
                     'email' => $userAPI['email'],
                     'username' => $userAPI['username'],
                     'phone' => $userAPI['phone'],
                     'website' => $userAPI['website']
                 ]);
                 $userFound->company()->update([
                    'user_id' => $userAPI['id'],
                    'name' => $userAPI['company']['name'],
                    'catchPhrase' => $userAPI['company']['catchPhrase'],
                    'bs' => $userAPI['company']['bs'],
                ]);
                 $userFound->address()->update([
                     'user_id' => $userAPI['id'],
                     'street' => $userAPI['address']['street'],
                     'suite' => $userAPI['address']['suite'],
                     'city' => $userAPI['address']['city'],
                     'zipcode' => $userAPI['address']['zipcode'],
                     'geo_lat' => $userAPI['address']['geo']['lat'],
                     'geo_lng' => $userAPI['address']['geo']['lng']
                 ]);
             }
             else {
                 User::insert([
                     'id' => $userAPI['id'],
                     'name' => $userAPI['name'],
                     'email' => $userAPI['email'],
                     'username' => $userAPI['username'],
                     'phone' => $userAPI['phone'],
                     'website' => $userAPI['website'],
                     'updated_at' => now()->toDateTimeString(),
                     'created_at' => now()->toDateTimeString()
                 ]);
                 Company::insert([
                     'user_id' => $userAPI['id'],
                     'name' => $userAPI['company']['name'],
                     'catchPhrase' => $userAPI['company']['catchPhrase'],
                     'bs' =>$userAPI['company']['bs'],
                     'updated_at' => now()->toDateTimeString(),
                     'created_at' => now()->toDateTimeString()
                 ]);
                 Address::insert([
                    'user_id' => $userAPI['id'],
                    'street' => $userAPI['address']['street'],
                    'suite' => $userAPI['address']['suite'],
                    'city' => $userAPI['address']['city'],
                    'zipcode' => $userAPI['address']['zipcode'],
                    'geo_lat' => $userAPI['address']['geo']['lat'],
                    'geo_lng' => $userAPI['address']['geo']['lng'],
                     'updated_at' => now()->toDateTimeString(),
                     'created_at' => now()->toDateTimeString()
                 ]);
             }
        }
        $oldUsers = User::whereNotIn('id', $userAPIIds)->get();
        foreach ($oldUsers as $oldUser) {
            $oldUser->delete();
        }

        echo "Import finished\n";
        return 0;
    }
}
