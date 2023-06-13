<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ImportUsersService;
use Tests\TestCase;

class UserPageTest extends TestCase
{

//    use RefreshDatabase;
    public function test_home()
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_login()
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_home_unauthourized()
    {
        $response = $this->get('/home');

        $response->assertStatus(302);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_sync_users_index()
    {
        $usersFactory = User::factory()->count(100)->make()->toArray();

        $userAPIIds = [];
        foreach ($usersFactory as $i => $user) {
            $userFound = User::find($user['email']);
            $userAPIIds[] = $user['email'];
            if($userFound) {
                ImportUsersService::updateUser($userFound, $user);
            }
            else {
                ImportUsersService::createUser($user);
            }
        }
        ImportUsersService::cleanOldUsers($userAPIIds);

        $usersDB = (array)User::all()->map(function ($el) {
            return [
                'name' => $el->name,
                'username' => $el->username,
                'email' => $el->email,
                'address' => [
                    'street' => $el->address->street,
                    'suite' => $el->address->suite,
                    'city' => $el->address->city,
                    'zipcode' => $el->address->zipcode,
                    'geo' => [
                        'lat' => $el->address->geo_lat,
                        'lng' => $el->address->geo_lng,
                    ]
                ],
                'phone' => $el->phone,
                'website' => $el->website,
                'company' => [
                    'name' => $el->company->name,
                    'catchPhrase' => $el->company->catchPhrase,
                    'bs' => $el->company->bs
                ]
            ];
        })->toArray();

        $res = 0;
        foreach ($usersFactory as $user) {
            foreach ($usersDB as $userDB) {
                if ($user['email'] == $userDB['email']) {
                    ++$res;
                    if ($user['name'] != $userDB['name']) {
                        $this->assertTrue(false,  'Different names');
                    }
                    if ($user['username'] != $userDB['username']) {
                        $this->assertTrue(false,  'Different usernames');
                    }
                    if ($user['address']['street'] != $userDB['address']['street']) {
                        $this->assertTrue(false,  'Different streets');
                    }
                    if ($user['address']['suite'] != $userDB['address']['suite']) {
                        $this->assertTrue(false,  'Different suites');
                    }
                    if ($user['address']['city'] != $userDB['address']['city']) {
                        $this->assertTrue(false,  'Different cities');
                    }
                    if ($user['address']['zipcode'] != $userDB['address']['zipcode']) {
                        $this->assertTrue(false,  'Different zipcodes');
                    }
                    if ($user['phone'] != $userDB['phone']) {
                        $this->assertTrue(false,  'Different phones');
                    }
                    if ($user['website'] != $userDB['website']) {
                        $this->assertTrue(false,  'Different websites');
                    }
                    if ($user['company']['name'] != $userDB['company']['name']) {
                        $this->assertTrue(false,  'Different country name');
                    }
                    if ($user['company']['catchPhrase'] != $userDB['company']['catchPhrase']) {
                        $this->assertTrue(false,  'Different lng');
                    }
                    if ($user['company']['bs'] != $userDB['company']['bs']) {
                        $this->assertTrue(false,  'Different lng');
                    }
                }

            }
        }

        $this->assertTrue($res === count($usersFactory), 'Everything is sync');
    }
}
