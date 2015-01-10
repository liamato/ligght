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


namespace ligght\Interfaces;

/**
 * HttpMethod
 *
 * @package ligght
 * @author  Marc Duran Olivé
 * @version 1.0.0
 * @since   1.0.0
 * @api
 * 
 * @used-by \ligght\Router             like a skeleton 
 * @used-by \ligght\RawHttpMethod      like a skeleton
 * 
 */
interface HttpMethod
{
    const ALL = 0;
    const GET = 1;
    const POST = 2;
    const PUT = 3;
    const DELETE = 4;
    const PATCH = 5;
    const OPTIONS = 6;
    const HEAD = 7;
    const DEBUG = 8;

    /**
     * getMethod
     *   - Return the id of the HTTP Method
     *   
     * @returns Array
     */
    public function getMethod();

    /**
     * getRoutes
     *   - Returns the seted routes
     *   
     * @returns Array
     */
    public function getRoutes();

    /**
     * setMethod
     *   - Sets the id of the HTTP Method
     *   
     * @param Number $method
     */
    public function setMethod($method);

    /**
     * setRoute
     *   - Sets asociates a route to a function
     *   
     * @param   string   $route          Route to execute the function
     * @param   mixed    $function       Function/Method to execute when was requested the route
     * @param   array    [$args = null]  Arguments of the route
     */
    public function setRoute($route, $function, $args = null);

    /**
     * setDefaultFunc
     *   - Sets the function to execute by default
     *   
     * @param   mixed    $function       Function/Method to execute when was requested an unset route
     * @param   array    [$args = null]  Arguments of the function
     */
    public function setDefaultFunc($function, $args = null);
}
