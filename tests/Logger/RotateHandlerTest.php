<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Logger\Tests;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Spiral\Core\BootloadManager;
use Spiral\Core\Container;
use Spiral\Logger\Bootloaders\MonologBootloader;

class RotateHandlerTest extends TestCase
{
    public function testRotateHandler()
    {
        $container = new Container();
        $container->get(BootloadManager::class)->bootload([MonologBootloader::class]);

        $autowire = new Container\Autowire('log.rotate', [
            'filename' => 'monolog.log'
        ]);

        /** @var RotatingFileHandler $handler */
        $handler = $autowire->resolve($container);
        $this->assertInstanceOf(RotatingFileHandler::class, $handler);

        $this->assertSame(Logger::DEBUG, $handler->getLevel());
    }
}