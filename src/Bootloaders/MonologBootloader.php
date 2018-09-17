<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Logger\Bootloaders;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Spiral\Core\Bootloaders\Bootloader;
use Spiral\Core\Container;
use Spiral\Debug\LogsInterface;
use Spiral\Logger\LogFactory;

class MonologBootloader extends Bootloader
{
    const BOOT = true;

    const SINGLETONS = [
        LogsInterface::class   => LogFactory::class,
        LoggerInterface::class => Logger::class
    ];

    /**
     * @param Container $container
     */
    public function boot(Container $container)
    {
        $container->bindInjector(Logger::class, LogFactory::class);
    }
}