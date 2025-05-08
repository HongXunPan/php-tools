<?php /** @noinspection PhpLanguageLevelInspection */

namespace HongXunPan\Tools\Event;

abstract class EventSubscriber
{
    private Event $event;

    abstract public function handle();
}