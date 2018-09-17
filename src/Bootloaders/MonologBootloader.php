<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Logger;

use Psr\Log\LoggerInterface;
use Spiral\Core\Bootloaders\Bootloader;
use Spiral\Core\Container;
use Spiral\Debug\LogsInterface;

class MonologBootloader extends Bootloader
{
    const BOOT = true;

    const SINGLETONS = [
        LogsInterface::class => LogFactory::class
    ];

    /**
     * @param Container $container
     */
    public function boot(Container $container)
    {
        $container->bindInjector(LoggerInterface::class, LogFactory::class);
    }
}