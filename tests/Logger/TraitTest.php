<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Logger\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Spiral\Core\Container;
use Spiral\Core\ContainerScope;
use Spiral\Logger\LogsInterface;
use Spiral\Logger\Traits\LoggerTrait;

class TraitTest extends TestCase
{
    use LoggerTrait;

    public function setUp(): void
    {
        $this->logger = null;
    }

    public function testNoScope(): void
    {
        $logger = $this->getLogger();
        $this->assertInstanceOf(NullLogger::class, $this->getLogger());
        $this->assertSame($logger, $this->getLogger());
    }

    public function testSetLogger(): void
    {
        $logger = new NullLogger();
        $this->setLogger($logger);
        $this->assertSame($logger, $this->getLogger());
    }

    public function testScope(): void
    {
        $logs = m::mock(LogsInterface::class);
        $logs->shouldReceive('getLogger')
            ->with(self::class)
            ->andReturn(new NullLogger());

        $container = new Container();
        $container->bind(LogsInterface::class, $logs);

        ContainerScope::runScope($container, function (): void {
            $this->assertInstanceOf(NullLogger::class, $this->getLogger());
        });
    }
}
