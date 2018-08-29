<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/21
 * Time: 19:39
 */
    namespace Weskiller\Stupider\Support\Traits;

    trait Original {

        use Singleton;

        public function __call($name, $args) {
            return call_user_func_array([static::$original,$name],$args);
        }

        public static function __callStatic($name, $arguments) {
            return call_user_func_array([static::$original,$name],$arguments);
        }

        public function __get($name) {
            return static::$original->$name;
        }

        /**
         * @return mixed
         */
        public static function getOriginal() {
            return static::$original;
        }

        /**
         * @param $original
         * @return mixed
         */
        public static function setOriginal($original) {
            return static::$original = $original;
        }

        /**
         * @param $original
         * @return $this
         */
        public function originalReplace($original) {
            static::$original = $original;
            return $this;
        }

    }