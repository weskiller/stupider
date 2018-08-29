<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/22
 * Time: 14:19
 */

    namespace Weskiller\Stupider\Event\Interfaces;

    /**
     * Interface EventProvider
     * @package Weskiller\Stupider\Event\Interfaces
     */
    interface EventProvider {
        public function getProvideEvent():array ;
        public function getEventRegister(string $event) :array ;
        public function add(string $event,string $tag,callable $callback,array $args):bool ;
        public function del(string $event,string $tag):void ;
    }