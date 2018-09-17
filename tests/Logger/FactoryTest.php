<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Logger\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Logger\Configs\MonologConfig;
use Spiral\Logger\LogFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;

class FactoryTest extends TestCase
{
    public function testDefaultLogger()
    {
        $factory = new LogFactory(new MonologConfig([]), new Container(), new EventDispatcher());
        $logger = $factory->getLogger();

        $this->assertNotEmpty($logger);
        $this->assertSame($logger, $factory->getLogger());
        $this->assertSame($logger, $factory->getLogger(LogFactory::DEFAULT));
    }
}