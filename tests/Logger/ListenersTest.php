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
use Spiral\Core\Container;
use Spiral\Logger\Config\MonologConfig;
use Spiral\Logger\Event\LogEvent;
use Spiral\Logger\LogFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ListenersTest extends TestCase
{
    public function testListenDebug()
    {
        $factory = new LogFactory(new MonologConfig([
            'globalLevel' => Logger::DEBUG
        ]), new Container(), new EventDispatcher());

        $logger = $factory->getLogger();
        $other = $factory->getLogger("other");

        /** @var LogEvent[]|array $records */
        $records = [];
        $factory->addListener(function (LogEvent $e) use (&$records) {
            $records[] = $e;
        });

        $logger->debug("debug");
        $other->alert("alert", ['context']);

        $this->assertCount(2, $records);
        $this->assertInstanceOf(\DateTimeInterface::class, $records[0]->getTime());
        $this->assertSame("default", $records[0]->getChannel());
        $this->assertSame(Logger::DEBUG, $records[0]->getLevel());
        $this->assertSame("DEBUG", $records[0]->getLevelName());
        $this->assertSame("debug", $records[0]->getMessage());
        $this->assertSame([], $records[0]->getContext());

        $this->assertSame("other", $records[1]->getChannel());
        $this->assertSame(Logger::ALERT, $records[1]->getLevel());
        $this->assertSame("ALERT", $records[1]->getLevelName());
        $this->assertSame("alert", $records[1]->getMessage());
        $this->assertSame(['context'], $records[1]->getContext());
    }

    public function testListenError()
    {
        $factory = new LogFactory(new MonologConfig([
            'globalLevel' => Logger::ERROR
        ]), new Container(), new EventDispatcher());

        $logger = $factory->getLogger();
        $other = $factory->getLogger("other");

        /** @var LogEvent[]|array $records */
        $records = [];
        $factory->addListener($l = function (LogEvent $e) use (&$records) {
            $records[] = $e;
        });

        $logger->debug("debug");
        $other->alert("alert", ['context']);

        $this->assertCount(1, $records);

        $this->assertInstanceOf(\DateTimeInterface::class, $records[0]->getTime());
        $this->assertSame("other", $records[0]->getChannel());
        $this->assertSame(Logger::ALERT, $records[0]->getLevel());
        $this->assertSame("ALERT", $records[0]->getLevelName());
        $this->assertSame("alert", $records[0]->getMessage());
        $this->assertSame(['context'], $records[0]->getContext());

        $factory->removeListener($l);

        $logger->debug("debug");
        $other->alert("alert", ['context']);
        $this->assertCount(1, $records);
    }
}