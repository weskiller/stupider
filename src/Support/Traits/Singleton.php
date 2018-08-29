<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/6
 * Time: 21:59
 */

    namespace Weskiller\Stupider\Support\Traits;

    trait Singleton {
        /**
         * @var static
         */
       private static $instance;

        /**
         * @param mixed $args
         * @return static
         */
        public static function getInstance(...$args) {
            if(!isset(self::$instance)) {
               self::$instance = new static(...$args);
            }
            return self::$instance;
        }
    }