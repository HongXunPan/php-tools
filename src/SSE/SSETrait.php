<?php

namespace HongXunPan\Tools\SSE;

/**
 * @property array $corsOriginHeaders
 */
trait SSETrait
{
//    protected $corsOriginHeaders = [
//        'Access-Control-Allow-Origin' => '*',
//        'Access-Control-Expose-Headers' => 'X-Requested-With,X-FAN-CACHE,X-Kang-Token',
//        'Access-Control-Allow-Methods' => 'GET,POST,OPTIONS,DELETE',
//        'Access-Control-Allow-Headers' => 'Content-Type,Cache-Control,X-Kang-Token,X-FAN-CACHE',
//        'Access-Control-Max-Age' => 1728000
//    ];

    protected function setSseHeader()
    {
        if (!headers_sent()) {
            header('X-Accel-Buffering: no');
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            $this->setCorsHeader();
        }

        set_time_limit(0); //防止超时
        ob_end_clean(); //清空（擦除）缓冲区并关闭输出缓冲
        ob_implicit_flush(1); //这个函数强制每当有输出的时候，即刻把输出发送到浏览器。这样就不需要每次输出（echo）后，都用flush()来发送到浏览器了
    }

    protected function setCorsHeader()
    {
        if (!$this->corsOriginHeaders) {
            $this->corsOriginHeaders = [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Credentials' => 'true',
            ];
        }
        foreach ($this->corsOriginHeaders as $key => $item) {
            header("$key: $item");
        }
    }

    /**
     * @param int $minutes
     * @return void
     */
    protected function setRetryTime($minutes)
    {
        $microMinutes = $minutes * 1000;
        $this->echoData("retry:$microMinutes\n\n");
    }

    protected function heartbeat($comment = 'heartbeat')
    {
        $msg = ":$comment\n\n";
        $this->echoData($msg);
    }

    /**
     * @param $data
     * @param int|string|null $id
     * @param $event
     * @return void
     */
    protected function send($data, $id = null, $event = 'message')
    {
        $response = '';
        if (empty($id)) {
            $id = uniqid();
        }
        $response .= "id:$id\n";
        $response .= "event:$event\n";
        if (!is_string($data)) {
            $data = json_encode($data);
        }
        $response .= "data:$data\n"; //如果数据很长，可以分成多行，最后一行用\n\n结尾，前面行都用\n结尾。
        $response .= "\n";
        $this->echoData($response);
    }

    /**
     * @param string $data
     * @return void
     */
    final protected function echoData($data)
    {
        echo $data;
        //刷新缓冲区
        if (ob_get_level() > 0) {
            ob_flush();
        }
        //将输出缓冲区的内容立即发送到客户端
        flush();
    }
}
