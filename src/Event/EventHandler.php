<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Logger\Event;

use Monolog\Handler\AbstractHandler;
use Spiral\Logger\LogFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventHandler extends AbstractHandler
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * @param int                      $level
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(int $level, EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        parent::__construct($level, true);
    }

    /**
     * @param array $record
     * @return bool|void
     */
    public function handle(array $record)
    {
        $this->dispatcher->dispatch(LogFactory::LOG_EVENT, new LogEvent(
            $record['datetime'],
            $record['channel'],
            $record['level'],
            $record['message'],
            $record['context']
        ));
    }
}