<?php /** @noinspection PhpUndefinedClassInspection */

/** @noinspection PhpLanguageLevelInspection */

namespace HongXunPan\Tools\Event;

abstract class EventSubscriber
{
    protected Event $event;
    const int PRIORITY = 0;

    final public function __construct(Event $event)
    {
        $this->event = $event;
    }

    abstract public function handle();
}