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

/**
 * Helpers
 *
 * @package ligght
 * @author  Marc Duran Olivé
 * @version 1.0.0
 * @since   1.0.0
 * 
 * @used-by \ligght\Exception
 * @uses \ligght\Exception  to set ligght exeptions
 */
class Helpers
{
    /**
     * getVarDump
     *   - Returns an string with the result of a var_dump()
     *   
     * @param    mixed   $arg              Variable that you want to get the var_dump()
     * @param    bool    [$inline = true]  It indicate if the return was inline or not
     * @returns  string                    String with the content of a var_dump()
     */
    public static function getVarDump($arg, $inline = true)
    {
        ob_start();
        call_user_func('var_dump', $arg);
        if ($inline === true) {
            return self::parseSpace(self::trimNl(ob_get_clean()));
        }
        return ob_get_clean();
    }

    /**
     * trimNl
     *   - Quits the '\n' or 'new line character' in a string
     *   
     * @param    string  $str   String to be parsed
     * @returns  string         The same string without '\n' or 'new line character'
     */
    public static function trimNl($str)
    {
        if (is_string($str)) {
            return implode("", explode("\n", $str));
        } else {
            throw new Exception\InvalidParam(
                'Invalid type of parameter $str: </b>'.Helpers::getVarDump($function).
                '<b>, expectated string'
            );
        }
    }

    /**
     * parseSpace
     *   - Converts the multiple spaces to a single space (like in HTML without <pre> tag)
     *   
     * @param    string   $str   String to be parsed
     * @returns  string          The same string without multiple spaces
     */
    public static function parseSpace($str)
    {
        if (is_string($str)) {
            if (isset(explode('  ', $str)[1])) {
                return self::parseSpace(implode(' ', explode('  ', $str)));
            }
            return $str;
        } else {
            throw new Exception\InvalidParam(
                'Invalid type of parameter $str: </b>'.Helpers::getVarDump($function).
                '<b>, expectated string'
            );
        }
    }
}
