<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/6
 * Time: 21:54
 */

    namespace Weskiller\Stupider\Server;
    use Weskiller\Stupider\Config;
    use Weskiller\Stupider\Manager;
    use Weskiller\Stupider\Support\Traits\Original;
    use Weskiller\Stupider\Event\Core as EventManager;
    use Weskiller\Stupider\Support\Traits\Record;
    use Weskiller\Stupider\Throwable\Concrete\SeverException;

    class Core {

        use Original;
        use Record {
            getRecord as getConf;
            setRaw as private setConf;
        }

        /**
         * @var \Swoole\Http\Server
         */
        private static $original = null;

        /**
         * raw data
         * @var array
         */
        public $config = [];

        /**
         * @var EventManager
         */
        private $eventManager = null;

        /**
         * Server constructor.
         */
        private function __construct() {
            $this->changeRaw('config');
            try {
                //loading config
                $this->loadConfig();
                //create event manager
                $this->eventManager = EventManager::getInstance();

                //loading eventRegister from config
                $this->getEventRegister();

                //execute register initialize trigger
                $this->eventManager->getProvider()->initialize();

                //create swoole server and make origin
                $this->originalReplace($this->createServer());

                //execute register create server trigger
                $this->eventManager->getProvider()->afterCreateServer($this);
            }
            catch (\Throwable $exception) {
                Manager::throwFatal($exception,true);
            }
        }

        /**
         * @return bool
         */
        public function start() :bool {
            try {
                //config server
                $this->setServer();
                //register event to server
                $this->registerEventToServer();
                //finally start
            }
            catch (\Throwable $exception) {
                Manager::throwFatal($exception,true);
            }
            return $this::$original->start();
        }

        /**
         * @return \Swoole\Http\Server
         * @throws SeverException
         */
        private function createServer() {
            if ($listen = $this->getConf('listen') and $port = $this->getConf('port')) {
                return new \Swoole\Http\Server($listen, $port);
            }
            throw new SeverException('invalid server config listen or port');
        }

        /**
         * set Swoole Server
         */
        private function setServer() {
            $set = $this->getConf('set');
            $logDir = Manager::getInstance()->getSysDirectory('log');
            foreach (['log_file' => 'log','pid_file' => 'pid'] as $name => $file) {
                if(isset($set[$name])) {
                    if(strpos('/', $set[$name]) === 0) {
                        break;
                    }
                    $set[$name] = "$logDir/{$set[$name]}";
                }
                else{
                    $set[$name] = "$logDir/$file";
                }
            }
            $this::$original->set($set);
        }

        /**
         * @return array|mixed
         */
        public function loadConfig() {
            $this->setConf(Config::getInstance()->get("server"));
            return $this;
        }

        /**
         * register eventProvider
         */
        protected function getEventRegister() {
            $eventProvider = $this->getConf('event.provider');
            try {
                $provider = new $eventProvider;
                $this->eventManager->registerProvider($provider);
            }
            catch (\Throwable $exception) {
                Manager::throwFatal($exception,true);
            }
        }


        /**
         * register event from provider to server
         */
        protected function registerEventToServer() {
            foreach ($this->eventManager->register() as $event => $callback) {
                $this::$original->on($event,function(...$args) use($callback) {
                    $this->eventManager::invoke($callback,$args);
                });
            }
        }

        /**
         * @return false|\Swoole\Http\Server
         */
        public function getSwooleServer() {
            if(!is_null($this::$original)) {
                return $this::$original;
            }
            return false;
        }
    }

