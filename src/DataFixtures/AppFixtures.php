<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
use App\Entity\City;
use App\Entity\User;
use App\Factory\LeadCustomFieldFactory;
use App\Factory\LeadFactory;
use App\Factory\SupportTicketFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Cities
        $cityList = array(
            'Casablanca',
            'Rabat',
            'Salé',
            'Kénitra',
            'Agadir',
            'Marrakech',
            'Tanger',
            'Tiznit',
            'Tinghir',
            'Tifelt',
            'Tetouan',
            'Temara',
            'Taza',
            'Taroudant',
            'Tarfaya',
            'Tantan',
            'Tamesna',
            'Tamensourte',
            'Sidi slimane',
            'Sidi rahal',
            'Marrakech',
        );

        foreach ($cityList as $cityName) {
            $city = new City();
            $city->setName($cityName);
            $manager->persist($city);
        }

        $manager->flush();

        // Users
        $userList = [
            [
                'givenName' => 'Jack',
                'familyName' => 'Nash',
                'email' => 'profile@sample.com',
                'roles' => ['ROLE_SUPER_ADMIN'],
                'password' => '$2y$13$kkXkSS.ugwH6GrwmjR57O.WwUTAHKIEyOUGjyrEfgxTu1L0N.HrJe',
                'apikey' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSU',
            ],
        ];

        $users = [];
        foreach ($userList as $userItem) {
            $user = new User();

            $user->setGivenName($userItem['givenName']);
            $user->setFamilyName($userItem['familyName']);
            $user->setEmail($userItem['email']);
            $user->setRoles($userItem['roles']);
            $user->setPassword($userItem['password']);
            if(isset($userItem['apikey'])){
                $apiToken = new ApiToken();
                $apiToken->setToken($userItem['apikey']);
                $user->addApiToken($apiToken);
                $manager->persist($apiToken);
            }


         
            $user->setApiKey($this->setKeyValue('apiKey', $userItem));
            $user->setTimezone($this->setKeyValue('timezone', $userItem));

            if (in_array('ROLE_AGENT_LEAD_CITY', $user->getRoles())) {
                foreach ($userItem['cities'] as $cityName) {
                    $city = $manager->getRepository(City::class)->findOneBy(['name' => $cityName]);
                    $user->addCity($city);
                }
            }

            $manager->persist($user);
            $users[] = $user;
        }

        $manager->flush();

    
        SupportTicketFactory::createMany(17);
    }

    private function setKeyValue($key, $array) {
        if (\array_key_exists($key, $array)) {
            return $array[$key];
        }

        return '';
    }
}
