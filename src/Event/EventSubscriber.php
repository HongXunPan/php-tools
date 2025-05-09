<?php /** @noinspection PhpLanguageLevelInspection */

namespace HongXunPan\Tools\Event;

abstract class EventSubscriber
{
    protected Event $event;

    final public function doHandle(Event $event)
    {
        $this->event = $event;
        $this->handle();
    }

    abstract public function handle();
}