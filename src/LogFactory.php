<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Logger;

use Psr\Log\LoggerInterface;
use Spiral\Logger\Event\LogEvent;

/**
 * Routes log information to various listeners.
 */
final class LogFactory implements LogsInterface
{
    /** @var callable[] */
    private $listeners = [];

    /**
     * @param string $channel
     * @return LoggerInterface
     */
    public function getLogger(string $channel): LoggerInterface
    {
        return new NullLogger([$this, 'log'], $channel);
    }

    /**
     * @param string $channel
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log($channel, $level, $message, array $context = [])
    {
        $e = new LogEvent(
            new \DateTime(),
            $channel,
            $level,
            $message,
            $context
        );

        foreach ($this->listeners as $listener) {
            call_user_func($listener, $e);
        }
    }

    /**
     * @param callable $listener
     */
    public function addListener(callable $listener)
    {
        if (!array_search($listener, $this->listeners, true)) {
            $this->listeners[] = $listener;
        }
    }

    /**
     * @param callable $listener
     */
    public function removeListener(callable $listener)
    {
        $key = array_search($listener, $this->listeners, true);
        if ($key !== null) {
            unset($this->listeners[$key]);
        }
    }
}