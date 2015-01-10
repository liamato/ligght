<?php

/**
 * ligght - The simple PHP router
 *
 * @author      Marc Duran Olivé <liamato97@gmail.com>
 * @copyright   2015 Marc Duran Olivé
 * @link
 * @license     http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version     1.0.0
 * @package     ligght
 *
 *
 *
 * Copyright (c) 2015, Marc Duran Olivé
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following
 * disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the
 * following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote
 * products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */


namespace ligght;

use \ligght\Exception\InvalidParam;
use \ligght\Exception\InvalidFormat;
use \ligght\Exception\InvalidPosition;

/**
 * Router
 *
 * @package ligght
 * @author  Marc Duran Olivé
 * @version 1.0.0
 * @since   1.0.0
 * @api
 *
 *
 * @uses \ligght\Interfaces\HttpMethod  to follow the interface structure
 * @uses \ligght\Exception              to set ligght exeptions
 * @uses \ligght\RawHttpMethod          to create the HTTP Method objects
 * @uses \ligght\Helpers                to use miscelanious methods
 *
 */
class Router implements Interfaces\HttpMethod
{
    /**
     * @var     int     $method  Id of the HTTP Method
     * @since   1.0.0
     */
    private $method;


    /**
     * @var     array   $routes  Routes asociated to the HTTP Method
     * @since   1.0.0
     */
    private $routes = array();


    /**
     * @var   callable  $defaultFunc    Function to asociate by default
     * @since   1.0.0
     */
    private $defaultFunc;


    /**
     * @var     object  $instance   Instance of self class
     * @since   1.0.0
     */
    private static $instance;


    /**
     * @var     array   $query   Array of pices of the request exploded by slashes
     * @since   1.0.0
     */
    private $query = array();


    /**
     * @var     array   $httpMethods    Instances of HTTP Methods
     * @since   1.0.0
     */
    private $httpMethods = array();


    /**
     * @var     array   $httpMethodNames  Name Id pairs of the HTTP Methods
     * @since   1.0.0
     */
    private $httpMethodNames = array();


    /**
     * Constructor
     *   - Set an Exception handler
     *   - Defines standard HTTP Methods
     */
    private function __construct()
    {
        set_exception_handler('ligght\Router::exception');
        $this->registerHttpMethod($this, 'ALL');
        foreach (array('GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD', 'DEBUG') as $method) {
            $this->registerHttpMethod(new RawHttpMethod(), $method);
        }
    }

    /**
     * Exception Handler
     * @param   object Exception    $exception
     */
    public static function exception($exception)
    {
        if (in_array('ligght\Exception', array_values(class_parents($exception)))) {
            print '<br><br><span style="font-family: \'Times New Roman\'; font-weight: bold; white-space: normal;">'.
                'Uncaught:</span>'.$exception;
        } else {
            print "<br><b>Fatal error</b>: Uncaught ".$exception.
                " thrown in <b>".$exception->getFile()."</b> on line <b>{$exception->getLine()}</b>";
        }
    }

    /**
     * getInstance
     * @return      object  Instance of self class
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * getQuery
     * @return      array   Array of pices of the request exploded by slashes
     */
    public function getQuery()
    {
        if (empty($this->query)) {
            $this->query = explode('/', $_SERVER['QUERY_STRING']);
        }
        return $this->query;
    }

    /**
     * getRoutes
     * @return      array   Routes asociated to the HTTP Method
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * getMethod
     * @return      int     Id of the HTTP Method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * getQueryMethod
     * @return      string  Uppercased string of the request Method name
     */
    private function getQueryMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * setDefaultFunc
     *   - Sets a function/method to execute when aren't route set to the query
     *
     * @param   callable    $function        Default function/method
     * @param   array       [$args = null]   Arguments for the function/method
     */
    public function setDefaultFunc($function, $args = null)
    {
        if (!defined('LOCKED')) {
            if (is_callable($function)) {
                $this->defaultFunc = $function;
            } else {
                $this->parseFunc($function, $code, $return);
                if ($code === 1) {
                    //print_r($return);
                    $this->defaultFunc = array(new $return[1], $return[3]);
                } elseif ($code === 0) {
                    die();
                }
            }
        } else {
            throw new InvalidPosition(
                '<code>Router::'.__FUNCTION__.
                '()</code></b> can\'t be placed after
                <b><code>Router::run()</code>'
            );
        }
    }

    /**
     * setRoute
     *   - Asociates a route with a function/method in the self HTTP Method
     *
     * @param  string    $route         Route where is asociated a function/method
     * @param  callable  $function      Function/method to be asociated to the route
     * @param  array     [$args = null] Arguments for the function/method
     */
    public function setRoute($route, $function, $args = null)
    {
        if (!defined('LOCKED')) {
            if (is_string($route)) {
                if (is_callable($function)) {
                    $this->routes[$route] = array($function, $args);
                } else {
                    $this->parseFunc($function, $code, $return);
                    if ($code === 1) {
                        $this->routes[$route] = array(array(new $return[1], $return[3]), $args);
                    } elseif ($code === 0) {
                        die();
                    }
                }
            } else {
                throw new InvalidParam(
                    'Invalid type of parameter $route: </b>'.Helpers::getVarDump($route).
                    '<b>, expectated string'
                );
            }
        } else {
            throw new InvalidPosition(
                '<code>Router::'.__FUNCTION__.
                '()</code></b> can\'t be placed after
                <b><code>Router::run()</code>'
            );
        }
    }

    /**
     * setMethod
     *   - Sets the Id of self HTTP Method
     *   
     * @param int $method Id of the self HTTP Method
     */
    public function setMethod($method)
    {
        if (is_int($method)) {
            if (!isset($this->method)) {
                $this->method = $method;
            }
        } else {
            throw new InvalidParam(
                'Invalid type of parameter $method: </b>'.Helpers::getVarDump($method).
                ',<br><b> expectated one of this constants:
                <ul>
                    <li><code>Router::GET</code></li>
                    <li><code>Router::POST</code></li>
                    <li><code>Router::PUT</code></li>
                    <li><code>Router::DELETE</code></li>
                    <li><code>Router::PATCH</code></li>
                    <li><code>Router::OPTIONS</code></li>
                    <li><code>Router::HEAD</code></li>
                    <li><code>Router::DEBUG</code></li>
                </ul>'
            );
        }
    }

    /**
     * setNewHttpMethod
     *   - Adds a custom HTTP Method
     * @param   object   Interfaces\HttpMethod $method    Object with \ligght\Interfaces\HttpMethod interface
     * @param   string   $name                            Name of HTTP Method
     */
    public function setNewHttpMehtod(Interfaces\HttpMethod $method, $name)
    {
        if (!defined('LOCKED')) {
            if ($method instanceof \ligght\Interfaces\HttpMethod) {
                if (is_string($name)) {
                    $this->registerHttpMethod($method, strtoupper($name));
                } else {
                    throw new InvalidParam(
                        'Invalid type of parameter $name: </b>'.Helpers::getVarDump($name).
                        '<b>, expectated string'
                    );
                }
            } else {
                throw new InvalidParam(
                    'Invalid type of parameter $method: </b>'.Helpers::getVarDump($method).
                    '<b>, expectated Object that implements \\ligght\\Interfaces\\HttpMethod'
                );
            }
        } else {
            throw new InvalidPosition(
                '<code>Router::'.__FUNCTION__.
                '()</code></b> can\'t be placed after
                <b><code>Router::run()</code>'
            );
        }
    }

    /**
     * parseFunc
     *   - If a function/method isn't callable it retun the callable form of the function/method
     *
     * @param     string      $func               Representation of the method like: 'Class::method' or 'Class->method'
     * @param     int|bool    [&$code = null]     Empty variable where it be assigned the code of preg_match
     * @param     array       [&$response = null] Result/s of preg_match
     * @return    int|bool    Same as $code
     */
    private function parseFunc($func, &$code = null, &$response = null)
    {
        if (!defined('LOCKED')) {
            if (gettype($func) === 'string') {
                $code = preg_match(
                    "/^([^\:]+)(\:\:|\->)([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\(?([^()]*)\)?$/",
                    $func,
                    $response
                );
                if ($code === 1) {
                    return $response;
                } elseif ($code === 0) {
                    throw new InvalidFormat(
                        '</b>Parameter $func: '.Helpers::getVarDump($func).', <b>has a wrong format</b><br>
                        [...] = Optional<br>
                        Function expectates:
                        <ul style="font-family: monospace; margin: 10px 0;">
                            <li>array($object, \'functionName\')</li>
                            <li>array(Class::getInstance(), \'functionName\')</li>
                            <li>\'Class::methodName[()]\'</li>
                            <li>\'Class->methodName[()]\'</li>
                            <li>\'functionName\'</li>
                            <li>function () {...}</li>
                        </ul>'
                    );
                } elseif ($code === false) {
                    return false;
                }
            } else {
                //echo '<pre>';
                //echo gettype($func);
                //print_r(debug_backtrace());
                throw new InvalidParam(
                    'Invalid type of parameter $func: </b>'.Helpers::getVarDump($func).
                    '<b>, expectated string'
                );
            }
        } else {
            throw new InvalidPosition(
                '<code>Router::'.__FUNCTION__.
                '()</code></b> can\'t be placed after
                <b><code>Router::run()</code>'
            );
        }
    }

    /**
     * route
     *   - Asociates a route and HTTP Method with a function/method
     *
     * @param  string    $method        Name of HTTP Method (it will be uppercased)
     * @param  string    $route         Route where is asociated a function/method
     * @param  callable  $function      Function/method to be asociated to the route
     * @param  array     [$args = null] Argument for the function/method
     */
    public function route($method, $route, $function, $args = null)
    {
        if (is_string($method)) {
            if (in_array(strtoupper($method), array_keys($this->httpMethodNames))) {
                $this->httpMethods[$this->httpMethodNames[strtoupper($method)]]->setRoute($route, $function, $args);
            } else {
                print '<br>Can\'t route a function to an unset HTTP Method';
                die();
            }
        } else {
            throw new InvalidParam(
                'Invalid type of parameter $method: </b>'.Helpers::getVarDump($method).
                '<b>, expectated string'
            );
        }
    }

    /**
     * registerHttpMethod
     *   - Register the HTTP Methods to the system
     *
     * @param  object  Interfaces\HttpMethod $method   Object with \ligght\Interfaces\HttpMethod interface
     * @param  string  $name                           Name of HTTP Method
     */
    private function registerHttpMethod(Interfaces\HttpMethod $method, $name)
    {
        if (!defined('LOCKED')) {
            if ($method instanceof Interfaces\HttpMethod) {
                if (is_string($name)) {
                    $id = count($this->httpMethods);
                    $this->httpMethods[$id] = $method;
                    $this->httpMethods[$id]->setMethod($id);
                    $this->httpMethodNames[$name] = $id;
                } else {
                    throw new InvalidParam(
                        'Invalid type of parameter $name: </b>'.Helpers::getVarDump($name).
                        '<b>, expectated string'
                    );
                }
            } else {
                throw new InvalidParam(
                    'Invalid type of parameter $method: </b>'.Helpers::getVarDump($method).
                    '<b>, expectated Object that implements \\ligght\\Interfaces\\HttpMethod'
                );
            }
        } else {
            throw new InvalidPosition(
                '<code>Router::'.__FUNCTION__.
                '()</code></b> can\'t be placed after
                <b><code>Router::run()</code>'
            );
        }
    }

    /**
     * validRoute
     *   - Check from the HTTP Methods routes if any one can be valid to the query
     *
     * @param    array  $route   Routes asociated to the query HTTP Method
     * @param    array  $query   Query exploded by slashes
     * @return   bool
     */
    private function validRoute($route, $query)
    {
        if (count($query) === count($route)) {
            foreach ($query as $key => $pice) {
                /*var_dump($key);
                print'<br>';*/

                if (isset($route[$key])) {
                    preg_match('/(\w*):(\w+)|(\w+)/', $route[$key], $match);
                    /*echo implode('/', $route).'<br>';
                    var_dump($match);*/
                    if (isset($match[1]) && !empty($match[1])) {
                        if (strpos($pice, $match[1]) !== false && strpos($pice, $match[1]) === 0) {



                            if ((count($query)-1) != $key) {

                                /*var_dump(array_shift($route));
                                var_dump(array_shift($query));*/
                                array_shift($route);
                                array_shift($query);
                                if ($this->validRoute(
                                    is_array($route) ? $route : array($route),
                                    is_array($query) ? $query : array($query)
                                )) {
                                    return true;
                                }
                                return false;
                            } else {
                                return true;
                            }



                        }
                        return false;
//                        return 'no cincideix';
                    } elseif (empty($match[1]) && !empty($match[2])) {

                        if ((count($query)-1) != $key) {
                            array_shift($route);
                            array_shift($query);
                            if ($this->validRoute(
                                is_array($route) ? $route : array($route),
                                is_array($query) ? $query : array($query)
                            )) {
                                return true;
                            }
                                return false;
                        } else {
                            return true;
                        }

                    } elseif (isset($match[3]) && !empty($match[3])) {
                        if (strpos($pice, $match[3]) !== false && strpos($pice, $match[3]) === 0) {

                            if ((count($query)-1) != $key) {
                                //var_dump(array_shift($route));
                                //var_dump(array_shift($query));

                                array_shift($route);
                                array_shift($query);
                                if ($this->validRoute(
                                    is_array($route) ? $route : array($route),
                                    is_array($query) ? $query : array($query)
                                )) {
                                    return true;
                                }
                                return false;
                            } else {
                                return true;

                            }



                        }
                        return false;
//                        return 'no coincideix';
                    }
                } else {
                    return false;
//                    return 'No prou llarga';
                }
            }
        }
        return false;
//        return '$query != $route';
    }

    /**
     * run
     *   - Executes all the procces to route the querys
     *
     * @return   mixed    The retun value of the function associated to the query route
     */
    public function run()
    {
        if (!defined('LOCKED')) {
            if (empty($this->defaultFunc)) {
                $this->setDefaultFunc(array($this, 'defaultFunc'));
            }
            define('LOCKED', true);

            $query = array_filter($this->getQuery());
            $routes = $this->httpMethods[$this->httpMethodNames[$this->getQueryMethod()]]->getRoutes();
//            print_r(array_keys($routes));

            $posibles = array();
            $args = array();

            if (!empty($routes)) {
                foreach (array_keys($routes) as $route) {
                    if ($this->validRoute(array_filter(explode('/', $route)), $query)) {
                        $posibles[] = $route;
                    }
                }

    //            print_r($posibles);

                foreach ($posibles as $key) {
//                    var_dump($key);
                    $args = $routes[$key][1];
                    if (!(count(explode(':', $key)) > 1)) {
    //                    var_dump($key);
                    } else {
    //                    var_dump($args); // $args passats per Router::route()
                        foreach (array_filter(explode('/', $key)) as $num => $pice) {
                            if (count(explode(':', $pice)) === 2) {
    //                            print_r($args);
    //                            var_dump(array_search(':'.explode(':', $pice)[1], $routes[$key][1]));
    //                            var_dump(explode(':', $pice));
                                if (empty(explode(':', $pice)[0])) {
                                    $args[array_search(':'.explode(':', $pice)[1], $routes[$key][1])] = $query[$num];
                                } else {
                                    $args[array_search(':'.explode(':', $pice)[1], $routes[$key][1])] =
                                        explode(explode(':', $pice)[0], $query[$num])[1];
                                }

    //                            var_dump(explode(explode(':', $pice)[0], $query[$num]));
                            }
                        }
                    }
                    $query = $key;
                }
            }

            if (empty($query)) {
                $query = '';
            }

            if (is_array($query)) {
                $query = implode('/', $query);
            }
    //            var_dump($args);

//                var_dump($query);
//                var_dump(isset($this->routes[$query]));
//                print_r($this->routes);



            if ($this->getQueryMethod() === array_search($this->method, $this->httpMethodNames)) {
                return call_user_func_array($this->getRoutes()[$query][0], $args);
            } elseif (isset($this->httpMethods[$this->httpMethodNames[$this->getQueryMethod()]]->getRoutes()
                            [$query][0])) {
                return call_user_func_array(
                    $this->httpMethods[$this->httpMethodNames[$this->getQueryMethod()]]->getRoutes()
                    [$query][0],
                    $args
                );
            } elseif (isset($this->routes[$query][0])) {
                return call_user_func_array($this->getRoutes()[$query][0], $args);
            } else {
                return call_user_func_array($this->defaultFunc, array());
            }
        } else {
            throw new InvalidPosition('<code>Router::'.__FUNCTION__.'()</code></b> can\'t be placed two times');
        }
    }

    /**
     * defaultFunc
     *   - defaultFunc by default
     */
    private function defaultFunc()
    {
        print <<<EOT
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>ligght framework</title>
        <style>
            div{
                width: 40%;
                margin: 0 auto;
            }
        </style>
    </head>
    <body>
        <div>
            <h1>ligght framework</h1>
        </div>
    </body>
</html>
EOT;
    }
}
