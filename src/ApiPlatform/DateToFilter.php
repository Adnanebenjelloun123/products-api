<?php

namespace App\ApiPlatform;

use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class DateToFilter implements FilterInterface
{
    public const TO_FILTER_CONTEXT = 'date_to';

    public function apply(Request $request, bool $normalization, array $attributes, array &$context)
    {
        $to = $request->query->get('to');

        if (!$to) {
            return;
        }

        $toDate = \DateTime::createFromFormat('Y-m-d', $to);

        if ($toDate) {
            $toDate = $toDate->setTime(0, 0, 0);

            $context[self::TO_FILTER_CONTEXT] = $toDate;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        // TODO: Implement getDescription() method.
        return [
            'to' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'openapi' => [
                    'description' => 'To date e.g. 2021-10-01'
                ]
            ]
        ];
    }
}