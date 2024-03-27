<?php

declare(strict_types=1);

namespace K_pi\Container;

use Github;
use K_pi;
use Symfony;

final class Definitions
{
    public static function build(): K_pi\Container
    {
        $container = new K_pi\Container();

        $container->define(
            K_pi\EnvVars::class,
            static fn () => new K_pi\EnvVars(),
        );

        $container->define(
            K_pi\Storage\Integrations::class,
            static fn (
                K_pi\Container $container,
            ) => new K_pi\Storage\Integrations(
                $container->get(
                    K_pi\Integration\Github\Discussion\Storage\Factory::class,
                ),
            ),
        );

        $container->define(
            K_pi\CheckReporter\Integrations::class,
            static fn (
                K_pi\Container $container,
            ) => new K_pi\CheckReporter\Integrations(
                $container->get(
                    K_pi\Integration\Github\CheckRun\CheckReporter\Factory::class,
                ),
                $container->get(
                    K_pi\Integration\Github\Status\CheckReporter\Factory::class,
                ),
            ),
        );

        self::buildCLI($container);
        self::buildExtractor($container);
        self::buildGithub($container);

        return $container;
    }

    private static function buildCLI(K_pi\Container $container): void
    {
        $container->define(
            Symfony\Component\Console\Application::class,
            static function (K_pi\Container $container) {
                $application = new Symfony\Component\Console\Application();
                $application->addCommands([
                    $container->get(K_pi\Command\CompileCommand::class),
                    $container->get(K_pi\Command\CheckCommand::class),
                ]);

                return $application;
            },
        );

        $container->define(
            K_pi\Command\CompileCommand::class,
            static fn (
                K_pi\Container $container,
            ) => new K_pi\Command\CompileCommand(
                $container->get(K_pi\Configuration\Extractor::class),
                $container->get(K_pi\Storage\Integrations::class),
            ),
        );

        $container->define(
            K_pi\Command\CheckCommand::class,
            static fn (
                K_pi\Container $container,
            ) => new K_pi\Command\CheckCommand(
                $container->get(K_pi\CheckReporter\Integrations::class),
                $container->get(K_pi\Configuration\Extractor::class),
                $container->get(K_pi\Storage\Integrations::class),
            ),
        );
    }

    private static function buildGithub(K_pi\Container $container): void
    {
        $container->define(
            K_pi\Integration\Github\Variables::class,
            static fn (
                K_pi\Container $container,
            ) => new K_pi\Integration\Github\Variables(
                $container->get(K_pi\EnvVars::class),
            ),
        );

        $container->define(Github\Client::class, static function (
            K_pi\Container $container,
        ) {
            $client = new Github\Client();
            $client->authenticate(
                $container
                    ->get(K_pi\Integration\Github\Variables::class)
                    ->getToken(),
                Github\Client::AUTH_ACCESS_TOKEN,
            );

            return $client;
        });

        $container->define(
            K_pi\Libs\KnpGithubApi\Github::class,
            static fn (
                K_pi\Container $container,
            ) => new K_pi\Libs\KnpGithubApi\Github(
                $container->get(Github\Client::class),
            ),
        );

        $container->define(
            K_pi\Libs\Lazy\Github::class,
            static fn (K_pi\Container $container) => new K_pi\Libs\Lazy\Github(
                static fn () => $container->get(
                    K_pi\Libs\KnpGithubApi\Github::class,
                ),
            ),
        );

        $container->define(
            K_pi\Integration\Github::class,
            static fn (K_pi\Container $container) => $container->get(
                K_pi\Libs\Lazy\Github::class,
            ),
        );

        $container->define(
            K_pi\Integration\Github\Discussion\Storage\Factory::class,
            static fn (
                K_pi\Container $container,
            ) => new K_pi\Integration\Github\Discussion\Storage\Factory(
                $container->get(K_pi\Integration\Github::class),
            ),
        );

        $container->define(
            K_pi\Integration\Github\CheckRun\CheckReporter\Factory::class,
            static fn (
                K_pi\Container $container,
            ) => new K_pi\Integration\Github\CheckRun\CheckReporter\Factory(
                $container->get(K_pi\Integration\Github::class),
                $container->get(K_pi\Integration\Github\Variables::class),
            ),
        );

        $container->define(
            K_pi\Integration\Github\Status\CheckReporter\Factory::class,
            static fn (
                K_pi\Container $container,
            ) => new K_pi\Integration\Github\Status\CheckReporter\Factory(
                $container->get(K_pi\Integration\Github::class),
                $container->get(K_pi\Integration\Github\Variables::class),
            ),
        );
    }

    private static function buildExtractor(K_pi\Container $container): void
    {
        $container->define(
            K_pi\Configuration\Extractor::class,
            static fn (K_pi\Container $container) => $container->get(
                K_pi\Configuration\Extractor\StrategyExtractor::class,
            ),
        );

        $container->define(
            K_pi\Configuration\Extractor\StrategyExtractor::class,
            static fn (
                K_pi\Container $container,
            ) => new K_pi\Configuration\Extractor\StrategyExtractor(
                $container->get(
                    K_pi\Configuration\Extractor\YamlFileExtractor::class,
                ),
            ),
        );

        $container->define(
            K_pi\Configuration\Extractor\YamlFileExtractor::class,
            static fn () => new K_pi\Configuration\Extractor\YamlFileExtractor(),
        );
    }
}
