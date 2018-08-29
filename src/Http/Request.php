<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/21
 * Time: 15:51
 */

    namespace Weskiller\Stupider\Http;


    use Weskiller\Stupider\Http\Abstracts\AbstractRequest;

    class Request extends AbstractRequest {

        /**
         * @param null|string $key
         * @return array|null|string
         */
        public function get(?string $key) {
            return $this->getVariable('get',$key);

        }

        /**
         * @param string|null $key
         * @return array | string | null
         */
        public function post(?string $key) {
            return $this->getVariable('post',$key);
        }

        /**
         * @param string|null $key
         * @return array | string | null
         */
        public function cookie(?string $key) {
            return $this->getVariable('cookie',$key);
        }

        /**
         * @param string|null $key
         * @return array | string | null
         */
        public function files(?string $key) {
            return $this->getVariable('files',$key);
        }

        /**
         * @param string|null $key
         * @return array | string | null
         */
        public function server(?string $key) {
            return $this->getVariable('server',$key);
        }

        /**
         * @param string|null $key
         * @return array | string | null
         */
        public function attributes(?string $key) {
            return $this->getVariable('attributes',$key);
        }

        /**
         * @return string
         */
        public function uri():string {
            return $this->getVariable('server','request_uri');
        }

        /**
         * @return string
         */
        public function queryString():string {
            return $this->getVariable('server','query_string') ?: '';
        }

        /**
         * @return string
         */
        public function host() :string {
            return $this->getVariable('header','host');
        }

        public function domain() :string {
            $host = $this->host();
            if(preg_match('/[^.]+\.[^.]+$/i',$host,$matches)) {
                return $matches[0];
            }
            return null;
        }

        public function scheme():string {
            return $this->getVariable('header','client_scheme') ?: 'http';
        }

        /**
         * @return string
         */
        public function method() {
            return $this->getVariable('server','request_method');
        }

        /**
         * @return string
         */
        public function client() :string {
            switch (true) {
                //add by nginx server ,compatible aliyun CDN
                case $relay = $this->getVariable('header','client_address'):
                    return explode(', ',$relay)[0];
                default:
                    return $this->getVariable('server','remote_addr');
            }
        }

        /**
         * @return array
         */
        public function relay() {
            switch (true) {
                //add by nginx server ,compatible aliyun CDN
                case $relay = $this->getVariable('header','client_address'):
                    return explode(', ',$relay);
                default:
                    return [$this->getVariable('server','remote_addr')];
            }
        }
    }