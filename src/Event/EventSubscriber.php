<?php /** @noinspection PhpUndefinedClassInspection */

/** @noinspection PhpLanguageLevelInspection */

namespace HongXunPan\Tools\Event;

abstract class EventSubscriber
{
    protected Event $event;
    const int PRIORITY = 0;

    final public function doHandle(Event $event)
    {
        $this->event = $event;
        $this->handle();
    }

    abstract public function handle();
}