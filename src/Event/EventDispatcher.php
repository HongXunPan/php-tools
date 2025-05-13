<?php /** @noinspection PhpUndefinedClassInspection */

/** @noinspection PhpLanguageLevelInspection */

namespace HongXunPan\Tools\Event;

class EventDispatcher
{
    private array $listeners = [];

    /**
     * 订阅事件
     *
     * @param string $eventName 事件名称
     * @param callable $listener 事件监听器
     * @param int $priority 优先级（数字越大，优先级越高）
     */
    public function addListener(string $eventName, array|string|callable $listener, $priority = 0)
    {
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
        $eventName = $event::class;
        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listenerData) {
                if (is_callable($listenerData['listener'])) {
                    call_user_func($listenerData['listener']);
                    continue;
                }
                if (is_array($listenerData['listener'])
                    && count($listenerData['listener']) == 2
                    && is_subclass_of($listenerData['listener'][0], 'HongXunPan\Tools\Event\EventSubscriber')
                ) {
                    $subscriber = new $listenerData['listener'][0]($event);
                    call_user_func([$subscriber, $listenerData['listener'][1]]);
                }
            }
        }
    }
}