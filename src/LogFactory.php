<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Logger;


use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;
use Spiral\Core\Container\InjectorInterface;
use Spiral\Core\Container\SingletonInterface;
use Spiral\Core\FactoryInterface;
use Spiral\Logger\Config\MonologConfig;
use Spiral\Logger\Event\EventHandler;
use Spiral\Logger\Exception\ConfigException;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LogFactory implements LogsInterface, InjectorInterface, SingletonInterface
{
    // Default logger channel (supplied via injection)
    public const DEFAULT = 'default';

    // Name of log event fired by global log handler
    public const LOG_EVENT = 'log';

    /** @var MonologConfig */
    private $config;

    /** @var LoggerInterface */
    private $default;

    /** @var FactoryInterface */
    private $factory;

    /** @var EventDispatcher */
    private $dispatcher;

    /** @var HandlerInterface|null */
    private $globalHandler;

    /**
     * @param MonologConfig    $config
     * @param FactoryInterface $factory
     * @param EventDispatcher  $dispatcher
     */
    public function __construct(
        MonologConfig $config,
        FactoryInterface $factory,
        EventDispatcher $dispatcher
    ) {
        $this->config = $config;
        $this->factory = $factory;
        $this->dispatcher = $dispatcher;
        $this->globalHandler = new EventHandler($config->getEventLevel(), $this->dispatcher);
    }

    /**
     * @inheritdoc
     */
    public function addListener(callable $listener)
    {
        $this->dispatcher->addListener(self::LOG_EVENT, $listener);
    }

    /**
     * @inheritdoc
     */
    public function removeListener(callable $listener)
    {
        $this->dispatcher->removeListener(self::LOG_EVENT, $listener);
    }

    /**
     * @inheritdoc
     */
    public function getLogger(string $channel = null): LoggerInterface
    {
        if ($channel === null || $channel == self::DEFAULT) {
            if (!empty($this->default)) {
                // we should use only one default logger per system
                return $this->default;
            }

            return $this->default = new Logger(
                self::DEFAULT,
                $this->getHandlers(self::DEFAULT),
                $this->getProcessors(self::DEFAULT)
            );
        }

        return new Logger(
            $channel,
            $this->getHandlers($channel),
            $this->getProcessors($channel)
        );
    }

    /**
     * @inheritdoc
     */
    public function createInjection(\ReflectionClass $class, string $context = null)
    {
        // always return default logger as injection
        return $this->getLogger();
    }

    /**
     * Get list of channel specific handlers.
     *
     * @param string $channel
     * @return array
     *
     * @throws ConfigException
     */
    protected function getHandlers(string $channel): array
    {
        // always include default handler
        $handlers = [];

        foreach ($this->config->getHandlers($channel) as $handler) {
            $handlers[] = $handler->resolve($this->factory);
        }

        $handlers[] = $this->globalHandler;

        return $handlers;
    }

    /**
     * Get list of channel specific log processors. Falls back to PsrLogMessageProcessor for now.
     *
     * @param string $channel
     * @return callable[]
     */
    protected function getProcessors(string $channel): array
    {
        return [new PsrLogMessageProcessor()];
    }
}