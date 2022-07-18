<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

/**
 * Class JwtDecorator
 * @package App\OpenApi
 */
final class RefreshJwtDecorator implements OpenApiFactoryInterface
{
    /**
     * RefreshJwtDecorator constructor.
     * @param OpenApiFactoryInterface $decorated
     */
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    /**
     * @param array $context
     * @return OpenApi
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Refresh_Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'refreshToken' => [
                    'type' => 'string',
                    'example' => '111111',
                ]
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'Refresh JWT Token',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authentication'],
                responses: [
                    '200' => [
                        'description' => 'Refresh JWT token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Refresh JWT token',
                requestBody: new Model\RequestBody(
                    description: 'Refresh JWT Token',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Refresh_Credentials',
                            ],
                        ],
                    ]),
                ),
            ),
        );
        $openApi->getPaths()->addPath('/api/refresh-token', $pathItem);

        return $openApi;
    }
}
