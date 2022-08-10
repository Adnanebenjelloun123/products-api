<?php

namespace App\ApiPlatform;

use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class TokenFilter implements FilterInterface
{
    public const TOKEN_FILTER_CONTEXT = 'token';

    public function apply(Request $request, bool $normalization, array $attributes, array &$context)
    {
        $token = $request->query->get('token');

        if (!$token) {
            return;
        }

        $context[self::TOKEN_FILTER_CONTEXT] = $token;
    }

    public function getDescription(string $resourceClass): array
    {
        // TODO: Implement getDescription() method.
        return [
            'token' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'openapi' => [
                    'description' => 'token'
                ]
            ]
        ];
    }
}