<?php

namespace App\Factory;

use App\Entity\City;
use App\Repository\CityRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<City>
 *
 * @method static City|Proxy createOne(array $attributes = [])
 * @method static City[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static City|Proxy find(object|array|mixed $criteria)
 * @method static City|Proxy findOrCreate(array $attributes)
 * @method static City|Proxy first(string $sortedField = 'id')
 * @method static City|Proxy last(string $sortedField = 'id')
 * @method static City|Proxy random(array $attributes = [])
 * @method static City|Proxy randomOrCreate(array $attributes = [])
 * @method static City[]|Proxy[] all()
 * @method static City[]|Proxy[] findBy(array $attributes)
 * @method static City[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static City[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static CityRepository|RepositoryProxy repository()
 * @method City|Proxy create(array|callable $attributes = [])
 */
final class CityFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'name' => self::faker()->firstNameFemale(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(City $city) {})
        ;
    }

    protected static function getClass(): string
    {
        return City::class;
    }
}
