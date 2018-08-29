<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/22
 * Time: 12:17
 */

    namespace Weskiller\Stupider\Swoole\WebScoket\Server\Abstracts;


    use Weskiller\Stupider\Swoole\Server\Abstracts\SwooleServerEventProvider;

    abstract class SwooleWebSocketServerEventProvider extends SwooleServerEventProvider {

        public static function onHandShake(\Swoole\WebSocket\Request $request, \Swoole\Http\Response $reponse):void {}

        public static function onOpen(\Swoole\WebSocket\Server $svr, \Swoole\Http\Response $response):void {}

        public static function onMessage(\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame):void {}

    }