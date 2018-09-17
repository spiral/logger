<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Logger\Configs;

use Monolog\Logger;
use Spiral\Core\Container\Autowire;
use Spiral\Core\InjectableConfig;
use Spiral\Logger\Exceptions\ConfigException;

class MonologConfig extends InjectableConfig
{
    const CONFIG = 'monolog';

    /** @var array */
    protected $config = [
        'globalHandler' => Logger::DEBUG,
        'handlers'      => []
    ];

    /**
     * @return int|null
     */
    public function getGlobalHandlerLevel(): ?int
    {
        return $this->config['globalHandler'] ?? null;
    }

    /**
     * @param string $channel
     * @return \Generator|Autowire[]
     *
     * @throws ConfigException
     */
    public function getHandlers(string $channel): \Generator
    {
        if (!isset($this->config['handlers'][$channel])) {
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