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
use Spiral\Logger\Bootloaders\MonologBootloader;
use Spiral\Logger\Configs\MonologConfig;
use Spiral\Logger\Traits\LoggerTrait;

class TraitTest extends TestCase
{
    use LoggerTrait;

    public function setUp()
    {
        $this->logger = null;
    }

    public function testNoScope()
    {
        $logger = $this->getLogger();
        $this->assertInstanceOf(NullLogger::class, $this->getLogger());
        $this->assertSame($logger, $this->getLogger());
    }

    public function testSetLogger()
    {
        $logger = new NullLogger();
        $this->setLogger($logger);
        $this->assertSame($logger, $this->getLogger());
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