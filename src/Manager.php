<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/13
 * Time: 21:05
 */

    namespace Weskiller\Stupider;

    use Weskiller\Stupider\Support\Traits\Record;
    use Weskiller\Stupider\Support\Traits\Singleton;
    use Weskiller\Stupider\Server\Core as Server;
    use Weskiller\Stupider\Throwable\Concrete\RunTimeException;

    class Manager {

        use Singleton;
        Use Record;

        const version = 0.1;

        /**
         * @var Config
         */
        private $config = null;

        /**
         * @var \Weskiller\Stupider\Server\Core|null
         */
        private $server = null;

        /**
         * Manager constructor.
         * @param $root
         */
        private function __construct($root) {
            $this->sysInfoInitialize();
            $this->sysDirectoryInitialize($root);
            $this->config = Config::getInstance($this->getSysDirectory('config'));
        }

        /**
         * real initialize
         */
        public function initialize() {
            $this->server = Server::getInstance();
            return $this;
        }

        /**
         * @return bool
         */
        public function start () :bool {
            return $this->server->start();
        }

        /**
         * @return bool
         */
        public function stop() :bool{
            $file = Config::getInstance()->get('server.set.pid_file');
            if(!(file_exists($file) && is_readable($file))) {
                echo "reading pid($file) failed!" . PHP_EOL;
                exit(0);
            }
            $content = file_get_contents($file);
            if(!empty($content) && $pid = intval($content)) {
                return posix_kill($pid, SIGTERM);
            }
            return false;
        }

        /**
         * @param string $path
         * @return array|mixed
         */
        public function getConf(string $path = '') {
            return $this->config->get($path);
        }

        /**
         * @param \Throwable $exception
         * @param bool $exit
         */
        public static function throwFatal(\Throwable $exception, bool $exit = true) {
            $file = mt_rand(1000,9999) . "-". date('YmdHis') . ".throwFatal";
            $dump = static::getExceptionDump($exception);
            $directory = static::getTmpDirectory() ."/throw";
            if(static::dumpContent($directory,$file,$dump)){
                $errMsg = <<<EOF
$exception

the throw exception dumped to $directory/$file
fix up and try again.
EOF;
                error_log($errMsg);
            }
            else {
                static::dumpLog($dump);
            }
            $exit && exit(0);
        }

        /**
         * @param string $content
         */
        public static function dumpLog(string $content) {
            $timestamp =  date('Y-m-d H:i:s');
            error_log(<<<EOF
>>>>>>>>>>>>>>>>>>>>>>>>>>DUMP LOG<<<<<<<<<<<<<<<<<<<<<<<<<<
$timestamp
$content
>>>>>>>>>>>>>>>>>>>>>>>>>>DUMP LOG<<<<<<<<<<<<<<<<<<<<<<<<<<
EOF
);
        }

        /**
         * @param string $directory
         * @param string $file
         * @param string $content
         * @return bool
         */
        public static function dumpContent(string $directory,string $file ,string $content) :bool {
            if(
                (is_dir($directory) && is_writeable($directory)) or
                mkdir($directory,0755,true)
            ) {
                $absolute = "$directory/$file";
                if($resource = fopen($absolute,'w+')) {
                    fwrite($resource,$content);
                    fclose($resource);
                    return true;
                }
            }
            return true;
        }

        /**
         * @param \Throwable $e
         * @return string
         */
        public static function getExceptionDump(\Throwable $e) :string{
            ob_start();
            var_dump($e);
            $dump = ob_get_contents();
            ob_end_clean();
            return $dump;
        }

        /**
         * @return string
         */
        public static function getTmpDirectory() {
            if(defined('TMPDIR')) {
                return TMPDIR;
            }
            return '/tmp';
        }

        /**
         * @param string|null $root
         */
        protected function sysDirectoryInitialize(?string $root) {
            //get root directory
            switch (true) {
                case $root:
                    $basedir = $root;
                    break;
                case $sysRoot = $this->getSysDirectory('root'):
                    $basedir = $sysRoot;
                    break;
                default:
                    $basedir = dirname(realpath($_SERVER['argv'][0]));
            }
            $this->setSysDirectory('root',$basedir);
            foreach (['log','tmp','config'] as $name) {
                $directory = "$basedir/$name";
                if(!file_exists($directory)) {
                    if(!mkdir($directory,0755,true)) {
                        Manager::throwFatal(new RunTimeException("$name directory $directory initialize failed(create error)!"),true);
                    }
                }
                if (!is_writeable($directory)) {
                    Manager::throwFatal(new RunTimeException("$name directory $directory initialize failed(permission denied)!"), true);
                }
                $this->setSysDirectory($name,$directory);
            }
        }

        /**
         * @param string $name
         * @return mixed|null
         */
        public function getSysDirectory(string $name = '') {
            return $this->getRecord("sys.directory.$name");
        }

        /**
         * @param string $name
         * @param string $absolute
         */
        protected function setSysDirectory(string $name,string $absolute) {
            $this->setRecord("sys.directory.$name",$absolute);
        }

        /**
         *
         */
        protected function sysInfoInitialize() {
            $this->setRecord('sys.script',explode('.',basename($_SERVER['argv'][0]))[0]);
            $this->setRecord('sys.version',static::version);
        }

        /**
         * @param string $name
         * @return mixed|null
         */
        public function getSysInfo(string $name) {
            return $this->getRecord("sys.$name");
        }
    }