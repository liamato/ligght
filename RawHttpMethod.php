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
 * RawHttpMethod
 *
 * @package ligght
 * @author  Marc Duran Olivé
 * @version 1.0.0
 * @since   1.0.0
 * @api
 * 
 * @used-by \ligght\Exception
 * @uses \ligght\Exception  to set ligght exeptions
 */
class RawHttpMethod implements Interfaces\HttpMethod
{
    /**
     * @var     int      $method       Id of the HTTP Method
     * @since   1.0.0
     */
    private $method;

    /**
     * @var     array    $routes       Routes asociated to the HTTP Method
     * @since   1.0.0
     */
    private $routes = array();

    /**
     * @var    callable  $defaultFunc  Function to asociate by default
     * @since   1.0.0
     */
    private $defaultFunc;

    
    /**
     * getMethod
     *   - It returns the if of the HTTP Method
     *   
     * @returns Number    Id of the HTTP Method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * getRoutes
     *   - It returns the seted routes of the HTTP Method
     *   
     * @returns Array    Routes seted
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * setMethod
     *   - Sets the id of the HTTP Method
     *   
     * @param Number    $method    The id of the HTTP Method
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
     * setRoute
     *   - Asociates a route to a function
     *   
     * @param String   $route         Route to execute the function
     * @param Mixed    $function      Function/Method to execute when was requested the route
     * @param Array    [$args = null] Arguments of the route
     */
    public function setRoute($route, $function, $args = null)
    {
        if (!defined('LOCKED')) {
            $this->routes[$route] = array($function, $args);
        } else {
            throw new InvalidPosition(
                '<code>RawHttpMethod::'.__FUNCTION__.
                '()</code></b> can\'t be placed after
                <b><code>Router::run()</code>'
            );
        }
    }

    /**
     * setDefaultFunc
     *   - Sets the function to execute by default
     *   
     * @param Mixed     $function       Function/Method to execute when was requested an unset route
     * @param Array     [$args = null]  Arguments of the function
     */
    public function setDefaultFunc($function, $args = null)
    {
        if (!defined('LOCKED')) {
            $this->defaultFunc = $function;
        } else {
            throw new InvalidPosition(
                '<code>Router::'.__FUNCTION__.
                '()</code></b> can\'t be placed after
                <b><code>Router::run()</code>'
            );
        }
    }
}
