<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/25
 * Time: 18:21
 */

    namespace Weskiller\Stupider\Http\Abstracts;

    use Weskiller\Stupider\Http\Interfaces\ResponseInterface;
    use Weskiller\Stupider\Support\Traits\Original;
    use Swoole\Http\Response;

    abstract class AbstractResponse implements ResponseInterface {

        use Original;

        /**
         * @var \Swoole\Http\Response
         *
         */
        protected static $original = null;

        /**
         * AbstractResponse constructor.
         * @param Response $response
         */
        protected function __construct(Response $response) {
            $this->originalReplace($response);
        }
    }