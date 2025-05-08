<?php /** @noinspection PhpLanguageLevelInspection */

namespace HongXunPan\Tools\Event;

class EventDispatcher
{
    private array $listeners = [];

    /**
     * 订阅事件
     *
     * @param Event $event 事件对象
     * @param callable $listener 事件监听器
     * @param int $priority 优先级（数字越大，优先级越高）
     */
    public function addListener(Event $event, callable $listener, int $priority)
    {
        $eventName = $event->event;
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }
        $this->listeners[$eventName][] = [
            'listener' => $listener,
            'priority' => $priority,
        ];
        // 按优先级排序
        usort($this->listeners[$eventName], function ($a, $b) {
            return $b['priority'] - $a['priority'];
        });
    }

    /**
     * 触发事件
     *
     * @param Event $event 事件对象
     */
    public function dispatch(Event $event)
    {
        $eventName = $event->event;
        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listenerData) {
                call_user_func($listenerData['listener'], 'handle');
            }
        }
    }
}