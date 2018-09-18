<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Logger\Config;

use Monolog\Logger;
use Spiral\Core\Container\Autowire;
use Spiral\Core\InjectableConfig;
use Spiral\Logger\Exception\ConfigException;

class MonologConfig extends InjectableConfig
{
    const CONFIG = 'monolog';

    /** @var array */
    protected $config = [
        'globalLevel' => Logger::DEBUG,
        'handlers'    => []
    ];

    /**
     * @return int
     */
    public function getEventLevel(): int
    {
        return $this->config['globalLevel'] ?? Logger::DEBUG;
    }

    /**
     * @param string $channel
     * @return \Generator|Autowire[]
     *
     * @throws ConfigException
     */
    public function getHandlers(string $channel): \Generator
    {
        if (empty($this->config['handlers'][$channel])) {
            return;
        }

        foreach ($this->config['handlers'][$channel] as $handler) {
            $wire = $this->wire($channel, $handler);
            if (!empty($wire)) {
                yield $wire;
            }
        }
    }

    /**
     * @param string $channel
     * @param mixed  $handler
     * @return null|Autowire
     *
     * @throws ConfigException
     */
    private function wire(string $channel, $handler): ?Autowire
    {
        if ($handler instanceof Autowire) {
            return $handler;
        }

        if (is_string($handler)) {
            return new Autowire($handler);
        }

        if (isset($handler['class'])) {
            return new Autowire($handler['class'], $handler['options'] ?? []);
        }

        throw new ConfigException("Invalid handler definition for channel `{$channel}`.");
    }
}