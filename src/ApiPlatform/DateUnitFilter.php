<?php

namespace App\ApiPlatform;

use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class DateUnitFilter implements FilterInterface
{
    public const UNIT_FILTER_CONTEXT = 'date_unit';

    public function apply(Request $request, bool $normalization, array $attributes, array &$context)
    {
        $unit = $request->query->get('unit');

        if (!$unit) {
            return;
        }

        $context[self::UNIT_FILTER_CONTEXT] = $unit;
    }

    public function getDescription(string $resourceClass): array
    {
        // TODO: Implement getDescription() method.
        return [
            'unit' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'openapi' => [
                    'description' => 'hour|day|month'
                ]
            ]
        ];
    }
}