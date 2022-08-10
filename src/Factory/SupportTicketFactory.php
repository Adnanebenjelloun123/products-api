<?php

namespace App\Factory;

use App\Entity\SupportTicket;
use App\Repository\SupportTicketRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<SupportTicket>
 *
 * @method static SupportTicket|Proxy createOne(array $attributes = [])
 * @method static SupportTicket[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static SupportTicket|Proxy find(object|array|mixed $criteria)
 * @method static SupportTicket|Proxy findOrCreate(array $attributes)
 * @method static SupportTicket|Proxy first(string $sortedField = 'id')
 * @method static SupportTicket|Proxy last(string $sortedField = 'id')
 * @method static SupportTicket|Proxy random(array $attributes = [])
 * @method static SupportTicket|Proxy randomOrCreate(array $attributes = [])
 * @method static SupportTicket[]|Proxy[] all()
 * @method static SupportTicket[]|Proxy[] findBy(array $attributes)
 * @method static SupportTicket[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static SupportTicket[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static SupportTicketRepository|RepositoryProxy repository()
 * @method SupportTicket|Proxy create(array|callable $attributes = [])
 */
final class SupportTicketFactory extends ModelFactory
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
            'email' => self::faker()->companyEmail(),
            'subject' => self::faker()->text(60),
            'message' => self::faker()->text(300),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(SupportTicket $supportTicket) {})
        ;
    }

    protected static function getClass(): string
    {
        return SupportTicket::class;
    }
}
