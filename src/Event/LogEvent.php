<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Logger\Event;

use Monolog\Logger;
use Symfony\Component\EventDispatcher\Event;

final class LogEvent extends Event
{
    const EVENT = 'log';

    /** @var \DateTimeInterface */
    private $time;

    /** @var string */
    private $channel;

    /** @var int */
    private $level;

    /** @var string */
    private $message;

    /** @var array */
    private $context;

    /**
     * @param \DateTimeInterface $time
     * @param string             $channel
     * @param int                $level
     * @param string             $message
     * @param array              $context
     */
    public function __construct(
        \DateTimeInterface $time,
        string $channel,
        int $level,
        string $message,
        array $context = []
    ) {
        $this->time = $time;
        $this->channel = $channel;
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getTime(): \DateTimeInterface
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @return string
     */
    public function getLevelName(): string
    {
        return Logger::getLevelName($this->level);
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}