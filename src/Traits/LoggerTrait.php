<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Logger\Traits;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Spiral\Core\ContainerScope;
use Spiral\Logger\LogsInterface;

/**
 * Logger trait provides access to the logger from the global container scope (if exists).
 */
trait LoggerTrait
{
    /** @var LoggerInterface|null @internal */
    private $logger = null;

    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get associated or create new instance of LoggerInterface.
     *
     * @return LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        if (!empty($this->logger)) {
            return $this->logger;
        }

        //We are using class name as log channel (name) by default
        return $this->logger = $this->createLogger();
    }

    /**
     * Create new instance of associated logger (on demand creation).
     *
     * @return LoggerInterface
     */
    private function createLogger(): LoggerInterface
    {
        $container = ContainerScope::getContainer();
        if (empty($container) || !$container->has(LogsInterface::class)) {
            return new NullLogger();
        }

        //We are using class name as log channel (name) by default
        return $container->get(LogsInterface::class)->getLogger(static::class);
    }
}