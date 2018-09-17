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
use Spiral\Core\BootloadManager;
use Spiral\Core\Container;
use Spiral\Debug\LogsInterface;
use Spiral\Logger\Bootloaders\MonologBootloader;
use Spiral\Logger\Configs\MonologConfig;

class HandlersTest extends TestCase
{
    /** @var Container */
    private $container;

    public function setUp()
    {
        $this->container = new Container();
        $this->container->get(BootloadManager::class)->bootload([MonologBootloader::class]);
    }

    protected function getLogger(): Logger
    {
        return $this->container->get(LogsInterface::class)->getLogger("test");
    }

    public function testNoHandlers()
    {
        $this->container->bind(MonologConfig::class, new MonologConfig());

        $logger = $this->getLogger();
        $this->assertSame("test", $logger->getName());
        $this->assertCount(0, $logger->getHandlers());
    }
}