<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/28
 * Time: 15:22
 */

    namespace Weskiller\Stupider\Support\Traits;

    trait Record {
        /**
         * the save raw data name;
         * @var string
         */
        protected static $name = '__raw';

        /**
         * default raw array
         * @var array
         */
        protected $__raw = ['__d' => 'this is default raw'];

        /**
         * @param string $name
         * @return mixed|null
         */
        public function getRecord(string $name = '') {
            $findPath = trim($name,'.');
            $record = $this->getRaw();
            if($findPath === '') {
                return $record;
            }
            $path  = explode('.',$findPath);
            while($path) {
                $value = array_shift($path);
                if(isset($record[$value])) {
                    $record = $record[$value];
                }
                else {
                    return null;
                }
            }
            return $record;
        }

        /**
         * @param string $name
         * @param $data
         * @param bool $force
         * @param string $coverName
         * @return bool
         */
        public function setRecord(string $name,$data,bool $force = false,string $coverName = '__raw'):bool {
            $name = trim($name,'.');
            if($name === '') {
                return false;
            }
            $path  = explode('.',$name);
            $record = &$this->getReferenceRaw();
            while($path) {
               $value = array_shift($path);
                if(is_array($record)) {
                    if(!isset($record[$value])) {
                        $record[$value] = [];
                    }
                }
                else {
                    if($force) {
                        $record = [ $value => [],$coverName => $record];
                    }
                    return false;
                }
                $record = &$record[$value];
            }

            //save data
            $record = $data;
            return true;
        }

        /**
         * @return array
         */
        public function getRaw() {
            return $this->{static::$name};
        }

        /**
         * @return mixed
         */
        protected function &getReferenceRaw() {
            return $this->{static::$name};
        }

        /**
         * @param array|null $raw
         */
        public function setRaw(?array $raw): void {
            $this->{static::$name} = $raw;
        }

        /**
         * @param string $name
         */
        public function changeRaw(string $name) {
            static::$name = $name;
        }
    }