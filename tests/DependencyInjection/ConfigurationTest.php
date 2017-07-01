<?php

namespace Sokil\CorsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function configurationProvider()
    {
        return [
            'noConfig' => [
                [],
                [
                    'allowedOrigins' => [],
                    'withCredentials' => false,
                    'maxAge' => null,
                ]
            ],
            'onlyAllowedOrigins' => [
                [
                    'allowedOrigins' => [
                        'http://example.com',
                        'https://example.com',
                    ],
                ],
                [
                    'allowedOrigins' => [
                        'http://example.com',
                        'https://example.com',
                    ],
                    'withCredentials' => false,
                    'maxAge' => null,
                ]
            ]
        ];
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testGetConfigTreeBuilder(
        $applicationConfig,
        $expectedBundleConfig
    ) {
        $configuration = new Configuration();
        $processor = new Processor();
        $actualBundleConfig = $processor->processConfiguration(
            $configuration,
            [
                'cors' => $applicationConfig,
            ]
        );

        $this->assertEquals(
            $expectedBundleConfig,
            $actualBundleConfig
        );
    }
}
