<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/10
 * Time: 16:21
 */

namespace App\Services;

use Carbon\Carbon;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class WebSocketService implements WebSocketHandlerInterface
{
    private $wsTable;
    public function __construct()
    {
        $this->wsTable = app('swoole')->wsTable;
    }

    // 连接建立时触发
    public function onOpen(Server $server, Request $request)
    {
        // 在触发 WebSocket 连接建立事件之前，Laravel 应用初始化的生命周期已经结束，你可以在这里获取 Laravel 请求和会话数据
        // 调用 push 方法向客户端推送数据，fd 是客户端连接标识字段
        \Log::info('websocket 连接建立:',[$request->fd]);
        $this->wsTable->set('fd:' . $request->fd, ['value' => $request->fd]);// 绑定fd到uid的映射
//        $server->push($request->fd, $request->fd);
    }

    // 收到消息时触发
    public function onMessage(Server $server, Frame $frame)
    {

        $data = json_decode( $frame->data , true );
        // 调用 push 方法向客户端推送数据
        $server->push($frame->fd, 'This is a message sent from WebSocket Server at ' . date('Y-m-d H:i:s'));
    }

    // 关闭连接时触发
    public function onClose(Server $server, $fd, $reactorId)
    {

        $this->wsTable->del('fd:' . $fd);// 解绑fd映射
        $server->push($fd, "Goodbye #{$fd}");
        \Log::info('WebSocket 连接关闭 '.$fd);
    }
}