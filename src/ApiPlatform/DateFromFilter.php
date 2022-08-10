<?php

namespace App\ApiPlatform;

use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class DateFromFilter implements FilterInterface
{
    public const FROM_FILTER_CONTEXT = 'date_from';

    public function apply(Request $request, bool $normalization, array $attributes, array &$context)
    {
        $from = $request->query->get('from');

        if (!$from) {
            return;
        }

        $fromDate = \DateTime::createFromFormat('Y-m-d', $from);

        if ($fromDate) {
            $fromDate = $fromDate->setTime(0, 0, 0);

            $context[self::FROM_FILTER_CONTEXT] = $fromDate;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        // TODO: Implement getDescription() method.
        return [
            'from' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'openapi' => [
                    'description' => 'From date e.g. 2021-10-01'
                ]
            ]
        ];
    }
}