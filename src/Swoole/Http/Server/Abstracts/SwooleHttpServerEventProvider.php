<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/21
 * Time: 17:27
 */

    namespace Weskiller\Stupider\Swoole\Http\Server\Abstracts;

    use Weskiller\Stupider\Swoole\Server\Abstracts\SwooleServerEventProvider;

    abstract class SwooleHttpServerEventProvider extends SwooleServerEventProvider {

        final public function onConnect(\Swoole\Server $server,int $workerId,int $reactorId):void {}

        final public function onReceive(\Swoole\Server $server,int $workerId,int $reactorId,string $data):void {}

        public function onRequest(\Swoole\Http\Request $req,\Swoole\Http\Response $rep):void {}
    }