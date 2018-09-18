<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Logger\Tests;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Spiral\Core\BootloadManager;
use Spiral\Core\Container;
use Spiral\Logger\LogsInterface;
use Spiral\Logger\Bootloader\MonologBootloader;
use Spiral\Logger\Config\MonologConfig;
use Spiral\Logger\Event\EventHandler;

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
        $this->assertCount(1, $logger->getHandlers());
    }

    public function testDefaultHandler()
    {
        $this->container->bind(MonologConfig::class, new MonologConfig([
            'globalHandler' => Logger::DEBUG
        ]));

        $logger = $this->getLogger();
        $this->assertSame("test", $logger->getName());
        $this->assertCount(1, $logger->getHandlers());
        $this->assertInstanceOf(EventHandler::class, $logger->getHandlers()[0]);
    }

    /**
     * @expectedException \Spiral\Logger\Exception\ConfigException
     */
    public function testInvalidHandler()
    {
        $this->container->bind(MonologConfig::class, new MonologConfig([
            'globalHandler' => Logger::DEBUG,
            'handlers'      => [
                'test' => [
                    ['what?']
                ]
            ]
        ]));

        $this->getLogger();
    }

    public function testHandlerObject()
    {
        $this->container->bind(MonologConfig::class, new MonologConfig([
            'handlers' => [
                'test' => [
                    new Container\Autowire(NullHandler::class)
                ]
            ]
        ]));

        $logger = $this->getLogger();

        $this->assertCount(2, $logger->getHandlers());
        $this->assertInstanceOf(NullHandler::class, $logger->getHandlers()[0]);
    }

    public function testBindedHandler()
    {
        $this->container->bind('nullHandler', new NullHandler());
        $this->container->bind(MonologConfig::class, new MonologConfig([
            'handlers' => [
                'test' => [
                    'nullHandler'
                ]
            ]
        ]));

        $logger = $this->getLogger();

        $this->assertCount(2, $logger->getHandlers());
        $this->assertInstanceOf(NullHandler::class, $logger->getHandlers()[0]);
        $this->assertSame($this->container->get('nullHandler'), $logger->getHandlers()[0]);
    }

    public function testConstructHandler()
    {
        $this->container->bind(MonologConfig::class, new MonologConfig([
            'handlers' => [
                'test' => [
                    [
                        'class' => NullHandler::class
                    ]
                ]
            ]
        ]));

        $logger = $this->getLogger();

        $this->assertCount(2, $logger->getHandlers());
        $this->assertInstanceOf(NullHandler::class, $logger->getHandlers()[0]);
    }

    public function testConstructWithOptionsHandler()
    {
        $this->container->bind(MonologConfig::class, new MonologConfig([
            'handlers' => [
                'test' => [
                    [
                        'class'   => NullHandler::class,
                        'options' => [
                            'level' => Logger::CRITICAL
                        ]
                    ]
                ]
            ]
        ]));

        $logger = $this->getLogger();

        $this->assertCount(2, $logger->getHandlers());
        $this->assertInstanceOf(NullHandler::class, $logger->getHandlers()[0]);
        $this->assertFalse($logger->getHandlers()[0]->isHandling(['level' => Logger::DEBUG]));
        $this->assertTrue($logger->getHandlers()[0]->isHandling(['level' => Logger::CRITICAL]));
    }
}