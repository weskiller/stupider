<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/21
 * Time: 17:27
 */

    namespace Weskiller\Stupider\Swoole\Server\Abstracts;

    abstract class SwooleServerEventProvider {

        public function onStart(\Swoole\Server $server):void {}

        public function onShutdown(\Swoole\Server $server):void {}

        public function onWorkerStart(\Swoole\Server $server,int $workerId):void {}

        public function onWorkerStop(\Swoole\Server $server,int $workerId):void {}

        public function onWorkerExit(\Swoole\Server $server,int $workerId):void {}

        public function onConnect(\Swoole\Server $server,int $workerId,int $reactorId):void {}

        public function onReceive(\Swoole\Server $server,int $workerId,int $reactorId,string $data):void {}

        public function onPacket(\Swoole\Server $server, string $data, array $clientInfo):void {}

        public function onClose(\Swoole\Server $server, int $fd, int $reactorId):void {}

        public function onBufferFull(\Swoole\Server $server,int $fd):void {}

        public function onBufferEmpty(\Swoole\Server $server,int $fd):void {}

        public function onTask(\Swoole\Server $server, int $taskId, int $fromWorkerId,mixed $data) {}

        public function onFinish(\Swoole\Server $server, int $taskId, string $data) {}

        public function onPipeMessage(\Swoole\Server $server, int $fromWorkerId, mixed $message):void {}

        public function onWorkerError(\Swoole\Server $server, int $workerId, int $workerPid, int $exitCode, int $signal):void {}

        public function onManagerStart(\Swoole\Server $server):void {}

        public function onManagerStop(\Swoole\Server $server):void {}
    }