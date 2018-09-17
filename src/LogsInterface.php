<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Logger;

use Psr\Log\LoggerInterface;

/**
 * LogsInterface is generic log factory interface.
 */
interface LogsInterface
{
    /**
     * Get pre-configured logger instance.
     *
     * @param string $channel
     * @return LoggerInterface
     */
    public function getLogger(string $channel): LoggerInterface;

    /**
     * Add new even listener.
     *
     * @param callable $listener
     */
    public function addListener(callable $listener);

    /**
     * Add LogEvent listener.
     *
     * @param callable $listener
     */
    public function removeListener(callable $listener);
}