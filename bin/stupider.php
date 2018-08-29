<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/13
 * Time: 19:48
 * description:
 */

    use Weskiller\Stupider\Manager;

    require __DIR__ . './../vendor/autoload.php';

    function CheckEnv() {
        if (php_sapi_name() != 'cli') {
            echo 'Must be start as php cli!';
            exit(0);
        }
        if(version_compare('7.1',PHP_VERSION,'>')) {
            echo 'PHP 7.1 or greater is required! , current is ' . PHP_VERSION;
            exit(1);
        }
    }

    function Useages () {
        echo 'Useages' . PHP_EOL;
        exit(0);
    }

    function Command($args) {
        switch ($args[1]) {
            case 'start':
                CommandStart();
                break;
            case 'stop':
                CommandStop();
                break;
            case 'restart':
                CommandRestart();
                break;
            case 'reload':
                CommandReload();
                break;
            case 'kill':
                CommandKill();
                break;
            default:
                Useages();
        }
    }

    function CommandStart() {
        $manager = Manager::getInstance(dirname(dirname(realpath($_SERVER['argv'][0]))))
            ->initialize();
        $manager->start();
    }

    function CommandStop() {
        Manager::getInstance(dirname(dirname(realpath($_SERVER['argv'][0]))))
            ->stop();
    }

    function CommandRestart() {
        CommandStop();
        CommandStart();
    }

    function CommandReload() {

    }

    function CommandKill() {

    }

    CheckEnv();
    Command($argv);