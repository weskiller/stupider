<?php
/**
 * Created by PhpStorm.
 * User: weskiller
 * Date: 2018/8/25
 * Time: 18:21
 */

    namespace Weskiller\Stupider\Http\Abstracts;

    use Weskiller\Stupider\Http\Interfaces\RequestInterface;
    use Weskiller\Stupider\Support\Traits\Original;
    use Swoole\Http\Request;

    abstract class AbstractRequest implements RequestInterface {

        use Original;
        /**
         * @var \Swoole\Http\Request
         *
         */
        protected static $original = null;

        /**
         * AbstractRequest constructor.
         * @param Request $request
         */
        protected function __construct(Request $request) {
            $this->originalReplace($request);
        }

        /**
         * @param string $property
         * @param string|null $key
         * @return array|string|null $value
         */
        protected function getVariable(string $property, ?string $key) {
            if (isset(static::$original->{$property})) {
                $value = static::$original->{$property};
                if (is_null($key)) {
                    return $value;
                }
                if (isset($value[$key])) {
                    return $value[$key];
                }
            }
            return null;
        }
    }