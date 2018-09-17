<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Logger\Tests;

use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Spiral\Core\BootloadManager;
use Spiral\Core\Container;
use Spiral\Core\ContainerScope;
use Spiral\Debug\Traits\LoggerTrait;
use Spiral\Logger\Bootloaders\MonologBootloader;
use Spiral\Logger\Configs\MonologConfig;

class TraitTest extends TestCase
{
    use LoggerTrait;

    public function setUp()
    {
        $this->logger = null;
    }

    public function testNoScope()
    {
        $this->assertInstanceOf(NullLogger::class, $this->getLogger());
    }

    public function testScope()
    {
        $container = new Container();
        $container->get(BootloadManager::class)->bootload([MonologBootloader::class]);
        $container->bind(MonologConfig::class, new MonologConfig());

        ContainerScope::runScope($container, function () {
            $this->assertInstanceOf(Logger::class, $this->getLogger());
            $this->assertSame(self::class, $this->getLogger()->getName());
        });
    }
}