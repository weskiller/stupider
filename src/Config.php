<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/13
 * Time: 19:42
 */

    namespace Weskiller\Stupider;

    use Weskiller\Stupider\Exceptions\Concrete\RunTimeException;
    use Weskiller\Stupider\Support\Traits\Record;
    use Weskiller\Stupider\Support\Traits\Singleton;

    class Config {

        use Singleton;

        use Record {
            getRecord as get;
            setRecord as private set;
        }

        const shadow = 'config.php';

        /**
         * @var string | null
         */
        public $basedir = null;

        /**
         * raw data
         * @var array
         */
        public $context = [];

        /**
         * Config constructor.
         * @param string $root
         */
        public function __construct(string $root) {
            $this->changeRaw('context');
            $this->setBasedir($root);
            $this->loadSpecifyConfig(dirname($root) .'/'. static::shadow);
        }

        /**
         * @param string $absolute
         * @return mixed
         * @throws RunTimeException
         */
        public function getSpecifyConfig(string $absolute) {
            if (is_file($absolute)) {
                try {
                    $config = require $absolute;
                    return $config;
                } catch (\Throwable $exception) {
                    Manager::throwFatal($exception, true);
                }
            }
            throw new RunTimeException("specify config($absolute) not found");
        }


        /**
         * @param string $file
         */
        public function loadSpecifyConfig(string $file) {
            try {
                $specifyConf = $this->getSpecifyConfig($file);
                foreach ($specifyConf as $conf) {
                    $this->load($conf);
                }
            }
            catch (\Throwable $exception) {
                Manager::throwFatal($exception,true);
            }
        }

        /**
         * @param string $conf
         * @return bool
         * @throws RunTimeException
         */
        public function load(string $conf) :bool {
            if (is_file($absolute = $this->getAbsolute($conf))) {
                $result = require $absolute;
                if (is_array($result)) {
                    $this->set($conf, $result);
                    return true;
                }
            }
            throw new RunTimeException("parse config file($absolute) error");
        }

        /**
         * @param string $conf
         * @return string
         * @throws RunTimeException
         */
        public function getAbsolute(string $conf): string {
            if (is_null($this->getBasedir())) {
                throw new RunTimeException('config directory not found');
            }
            return "$this->basedir/$conf.php";
        }

        /**
         * @return string | null
         */
        public function getBasedir() {
            return $this->basedir;
        }

        /**
         * @param null|string $basedir
         */
        public function setBasedir(?string $basedir): void {
            $this->basedir = $basedir;
        }
    }

