<?php


namespace App\Services;


use App\Components\ImportUsersClient;
use App\Models\Address;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ImportUsersService
{
    public function __construct()
    {
        echo "Start\n";

        echo "Download users...\n";

        $usersAPI = $this->getUsersFromAPI();

        echo "Importing...\n";
        $userAPIEmails = [];
        foreach ($usersAPI as $i => $userAPI) {
            $userDB = User::find($userAPI['email']);
            $userAPIEmails[] = $userAPI['email'];
            if($userDB) {
                self::updateUser($userDB, $userAPI);
                echo "Users are updated \n";
            }
            else {
                self::createUser($userAPI);
                echo "Users are imported \n";
            }
        }
        self::cleanOldUsers($userAPIEmails);


    }

    public static function getUsersFromAPI()
    {
        $import = new ImportUsersClient();

        $response = $import->client->request('GET', 'https://jsonplaceholder.typicode.com/users');

        return json_decode($response->getBody()->getContents(), true);
    }

    public static function updateUser($userDB, $userAPI) {
        $userDB->update([
            'name' => $userAPI['name'],
            'email' => $userAPI['email'],
            'username' => $userAPI['username'],
            'phone' => $userAPI['phone'],
            'website' => $userAPI['website']
        ]);
        $userDB->company()->update([
            'user_id' => $userAPI['id'],
            'name' => $userAPI['company']['name'],
            'catchPhrase' => $userAPI['company']['catchPhrase'],
            'bs' => $userAPI['company']['bs'],
        ]);
        $userDB->address()->update([
            'user_id' => $userAPI['id'],
            'street' => $userAPI['address']['street'],
            'suite' => $userAPI['address']['suite'],
            'city' => $userAPI['address']['city'],
            'zipcode' => $userAPI['address']['zipcode'],
            'geo_lat' => $userAPI['address']['geo']['lat'],
            'geo_lng' => $userAPI['address']['geo']['lng']
        ]);
    }

    public static function createUser($userAPI) {
        $trashedUser = User::withTrashed()->where('email', $userAPI['email'])->first();
        if($trashedUser) {
            $trashedUser->forceDelete();
        }

        $user = User::create([
            'name' => $userAPI['name'],
            'email' => $userAPI['email'],
            'password' => Hash::make('Zxcvbnm1'),
            'username' => $userAPI['username'],
            'phone' => $userAPI['phone'],
            'website' => $userAPI['website'],
        ])->toArray();

        Company::create([
            'user_id' => $user['id'],
            'name' => $userAPI['company']['name'],
            'catchPhrase' => $userAPI['company']['catchPhrase'],
            'bs' =>$userAPI['company']['bs']
        ]);
        Address::create([
            'user_id' => $user['id'],
            'street' => $userAPI['address']['street'],
            'suite' => $userAPI['address']['suite'],
            'city' => $userAPI['address']['city'],
            'zipcode' => $userAPI['address']['zipcode'],
            'geo_lat' => $userAPI['address']['geo']['lat'],
            'geo_lng' => $userAPI['address']['geo']['lng']
        ]);
    }

    public static function cleanOldUsers(array $userAPIEmails) {
        $oldUsers = User::whereNotIn('email', $userAPIEmails)->get();
        foreach ($oldUsers as $oldUser) {
            $oldUser->delete();
        }
    }
}
