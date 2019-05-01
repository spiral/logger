<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Logger\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Spiral\Logger\Event\LogEvent;
use Spiral\Logger\LogFactory;

class FactoryTest extends TestCase
{
    public function testEvent()
    {
        $f = new LogFactory();
        $f->addListener(function (LogEvent $event) {
            $this->assertSame("error", $event->getMessage());
            $this->assertSame("default", $event->getChannel());
            $this->assertSame(LogLevel::CRITICAL, $event->getLevel());
        });

        $l = $f->getLogger('default');

        $l->critical("error");
    }
}