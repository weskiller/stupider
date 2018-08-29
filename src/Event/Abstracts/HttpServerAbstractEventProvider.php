<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/21
 * Time: 15:38
 */

    namespace Weskiller\Stupider\Event\Abstracts;

    use Weskiller\Stupider\Event\Interfaces\EventProvider;
    use Weskiller\Stupider\Manager;
    use Weskiller\Stupider\Server\Core;
    use Weskiller\Stupider\Swoole\Http\Server\Abstracts\SwooleHttpServerEventProvider;

    abstract class HttpServerAbstractEventProvider extends SwooleHttpServerEventProvider implements EventProvider  {

        const defaultEventTag = 'onDefault';

        /**
         * provide event to swoole http server
         * @var array
         */
        private $provideEvent = [];

        /**
         * save register event
         * @var array
         */
        private $registeredEvent = [];

        /**
         * define support event and call
         * StupiderHttpServerServerEvent constructor.
         */
        public function __construct() {
            $this->provideEvent = [
                'start' => 'onStart',
                'workerstart' => 'onWorkerStart',
                'request' => 'onRequest',
                'task' => 'onTask',
                'finish' => 'onFinish',
            ];
        }

        /**
         * @param string $event
         * @param string $tag
         * @param callable $callback
         * @param array|null $args
         * @return bool
         */
        public function add(string $event,string $tag,callable $callback,?array $args):bool {
            if($this->isProvidedEvent($event) and $registerCall = $this->eventRegisterParser($callback,$args)) {
                if($this->isRegisteredEvent($event)) {
                    return $this->addEventRegister($event, $tag,$registerCall);
                }
                if($tag!= static::defaultEventTag) {
                    return $this->createEventRegister($event,$tag,$registerCall);
                }
            }
            return false;
        }

        /**
         * @param string $event
         * @param string $tag
         */
        public function del(string $event,string $tag):void {
            if($this->isRegisteredEvent($event)) {
                $this->deleteEventRegister($event,$tag);
            }
        }

        /**
         * @param $callback
         * @param $args
         * @return array|bool
         */
        protected function eventRegisterParser($callback,array $args = null) {
            if(is_callable($callback)) {
                return [
                    'callable' => $callback,
                    'args' => $args,
                ];
            }
            return false;
        }

        /**
         * @param $event
         * @param $tag
         * @param $registerCall
         * @return bool
         */
        protected function createEventRegister($event, $tag, $registerCall) :bool {
            $this->registeredEvent[$event] = [$tag => $registerCall];
            return true;
        }

        /**
         * @param $event
         * @param $tag
         * @param $registerCall
         * @return bool
         */
        protected function addEventRegister(string $event, string $tag, array $registerCall) :bool {
            $this->registeredEvent[$event][$tag] = $registerCall;
            return true;
        }

        /**
         * @param string $event
         * @param string $tag
         */
        protected function deleteEventRegister(string $event, string $tag) :void {
            unset($this->registeredEvent[$event][$tag]);
        }

        /**
         * @param string $event
         * @return bool
         */
        public function isProvidedEvent(string $event) {
            return array_key_exists($event,$this->provideEvent);
        }

        /**
         * @param string $event
         * @return bool
         */
        public function isRegisteredEvent(string $event){
            return isset($this->registeredEvent[$event]);
        }

        /**
         * @param string $event
         * @return mixed
         */
        public function getRegisterEvent(string $event) {
            return $this->registeredEvent[$event];
        }

        /**
         * return this provider event
         * @return array
         */
        public function getProvideEvent() :array {
            return array_keys($this->provideEvent);
        }


        /**
         * @param string $event
         * @return bool|mixed
         */
        public function getProvideEventCall(string $event) {
            if(isset($this->provideEvent[$event])) {
                return $this->provideEvent[$event];
            }
            return false;
        }

        /**
         * @param string $event
         * @return array
         */
        public function getEventRegister(string $event) :array {
            $eventCall = [];
            if($this->isRegisteredEvent($event)) {
                $eventCall = $this->getRegisterEvent($event);
            }
            $defaultCall = $this->getDefaultEventCallback($event);
            if(!is_null($defaultCall)){
                $eventCall[static::defaultEventTag] = $this->eventRegisterParser($defaultCall);
            }
            return $eventCall;
        }

        /**
         * @param $event
         * @return array|null
         */
        public function getDefaultEventCallback($event) {
            $method = $this->getProvideEventCall($event);
            if(method_exists($this,$method)) {
                return [$this, $method];
            }
            return null;
        }


        /**
         * initialize before registered
         */
        public function initialize() {}

        /**
         * after server created
         * @param Core $server
         */
        public function afterCreateServer(Core $server) {}

        /**
         * on swoole manager process start
         * @param \Swoole\Server $server
         */
        public function onStart(\Swoole\Server $server): void {
            swoole_set_process_name(Manager::getInstance()->getSysInfo('script') . " Manager");
        }

        /**
         * on swoole worker processt start
         * @param \Swoole\Server $server
         * @param int $workerId
         */
        public function onWorkerStart(\Swoole\Server $server, int $workerId): void {
            if($server->taskworker) {
                swoole_set_process_name(Manager::getInstance()->getSysInfo('script') . " TaskWorker-$workerId");
            }
            else {
                swoole_set_process_name(Manager::getInstance()->getSysInfo('script') . " Worker-$workerId");
            }
        }

        public function onRequest(\Swoole\Http\Request $req, \Swoole\Http\Response $rep): void {
            @$rep->end();
        }
    }