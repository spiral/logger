<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Logger\Bootloader;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Spiral\Core\Bootloader\Bootloader;
use Spiral\Core\Container;
use Spiral\Logger\LogFactory;
use Spiral\Logger\LogsInterface;

class MonologBootloader extends Bootloader implements Container\SingletonInterface
{
    const BOOT = true;

    const SINGLETONS = [
        LogsInterface::class   => LogFactory::class,
        LoggerInterface::class => Logger::class
    ];

    const BINDINGS = [
        'log.rotate' => [self::class, 'logRotate']
    ];

    /**
     * @param Container $container
     */
    public function boot(Container $container)
    {
        $container->bindInjector(Logger::class, LogFactory::class);
    }

    /**
     * @param string $filename
     * @param int    $level
     * @param int    $maxFiles
     * @param bool   $bubble
     * @return HandlerInterface
     */
    public function logRotate(
        string $filename,
        int $level = Logger::DEBUG,
        int $maxFiles = 0,
        bool $bubble = false
    ): HandlerInterface {
        $handler = new RotatingFileHandler(
            $filename,
            $maxFiles,
            $level,
            $bubble,
            null,
            false
        );

        return $handler->setFormatter(
            new LineFormatter("[%datetime%] %level_name%: %message%\n")
        );
    }
}