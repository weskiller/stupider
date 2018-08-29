<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/25
 * Time: 18:26
 */

    namespace Weskiller\Stupider\Http\Interfaces;

    interface RequestInterface {
        public function get(?string $key);
        public function post(?string $key);
        public function uri();
        public function queryString();
        public function host();
        public function scheme();
        public function method();
        public function client();
    }