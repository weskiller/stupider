<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/13
 * Time: 19:55
 */
    return [
        'listen' => '0.0.0.0',
        'port' => '9090',
        'set' => [
            'daemonize' => false,
            'worker_num' => 2,
            'task_worker_num' => 4,
            'package_max_length' => 1024 * 1024 * 1024,
            //'user' => 'weskiller',
            //'group' => 'weskiller',
            'log_file' => 'server.log',
            'pid_file' => 'server.pid',
        ],
        //swoole http server event
        'event' => [
            'provider' => \Weskiller\Stupider\Event\Concrete\ServerEventProvider::class,
        ],
    ];
