<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/20
 * Time: 19:55
 */

    namespace Weskiller\Stupider\Event;

    use Weskiller\Stupider\Event\Abstracts\HttpServerAbstractEventProvider;
    use Weskiller\Stupider\Support\Traits\Original;

    class Core {

        use Original;

        /**
         * @var HttpServerAbstractEventProvider
         */
        private static $original = null;

        private $event = null;

        private function __construct() {}

        private $invoke = [];

        /**
         * @param $eventProvider
         */
        public function registerProvider(HttpServerAbstractEventProvider $eventProvider) {
            $this->originalReplace($eventProvider);
        }

        public function register() {
            $eventProvider = $this::$original;
            foreach ($eventProvider->getProvideEvent() as $eventName) {
                $this->event[$eventName] = $eventProvider->getEventRegister($eventName);
            }
            return $this->event;
        }

        public function isRegistered() {
            return ! is_null($this->event);
        }

        public function getProvider() {
            return static::$original;
        }

        public static function invoke(array $register,array $callArgs) {
            foreach ($register as $tag => $event) {
                //echo "called on $tag" . PHP_EOL;
                call_user_func_array($event['callable'], $event['args'] ?: $callArgs);
            }
        }
    }