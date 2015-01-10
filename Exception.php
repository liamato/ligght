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
 * Exception
 *
 * @package ligght
 * @author  Marc Duran Olivé
 * @version 1.0.0
 * @since   1.0.0
 * 
 * @used-by \ligght\Exception\InvalidFormat     like a skeleton 
 * @used-by \ligght\Exception\InvalidParam      like a skeleton
 * @used-by \ligght\Exception\InvalidPosition   like a skeleton
 * 
 * @uses \ligght\Helpers                to use miscelanious methods
 *
 */
class Exception extends \Exception
{

    /**
     * __toString
     *   - The method that returns the message with format
     *   
     * @returns   string    To see it with good formating only print it
     */
    public function __toString()
    {
        return $this->parseB($this->setToString(
            $this->getClassName($this->getClass()),
            Helpers::parseSpace($this->parseBr($this->parseUl($this->parseCode($this->message))))
        ));
    }

    /**
     * setToString
     *   - Default format to ligght Exceptions
     *   
     * @param     string   $class     Name of the self class
     * @param     string   $message   The message of the exception
     * @returns   string              String with default format to ligght Exceptions
     */
    protected function setToString($class, $message)
    {
        return '<div style="font-family: \'Times New Roman\'; padding: 30px 0; white-space: normal; margin: 0;">
        <p style="margin: 0;">Throwed exeption ligght\\<b>' . $class .'</b></p>
        <span style="margin-left: 1em;">└ Message: <b>'.$message.'</b></span>
        <p style="margin: 0;"><span style="margin-left: 1em;">└ File: <b>'.$this->file.'</b></span></p>
        <p style="margin: 0;"><span style="margin-left: 1em;">└ Line: <b>'.$this->line.'</b></span></p></div>';
    }

    /**
     * getClass
     *   - Method to get the self class name
     *   
     * @returns   string    Name of self class
     */
    protected function getClass()
    {
        return __CLASS__;
    }

    /**
     * getClassName
     *   - Parses FQ Class Name to get the Name of the class
     *   
     * @param     string   $class   Full Qualified Class Name
     * @returns   string            Class name
     */
    protected function getClassName($class)
    {
        return explode('\\', $class)[count(explode('\\', $class)) - 1];
    }

    /**
     * parseBr
     *   - Changes the <br> tags to more semantical solution for ligght Exceptions
     *   
     * @param     string   $message  The string to parse
     * @returns   string             The same string with more semantical solution for ligght Exceptions
     */
    protected function parseBr($message)
    {
        return implode(
            Helpers::trimNl('</span><br><span style="margin-left: 100px; display: inline-block;">'),
            explode('<br>', $message)
        );
    }

    /**
     * parseB
     *   - Changes the <b> tags to more semantical solution for ligght Exceptions
     *   
     * @param     string   $message  String to parse
     * @returns   string             The same string with more semantical solution for ligght Exceptions
     */
    protected function parseB($message)
    {
        return implode(
            '<span style="font-weight: bold;">',
            explode('<b>', implode('</span>', explode('</b>', $message)))
        );
    }

    /**
     * parseCode
     *   - Changes the <code> tags to more semantical solution for ligght Exceptions
     *   
     * @param     string   $message  String to parse
     * @returns   string             The same string with more semantical solution for ligght Exceptions
     */
    protected function parseCode($message)
    {
        return implode(
            '<span style="font-family: monospace;">',
            explode('<code>', implode('</span>', explode('</code>', $message)))
        );
    }

    /**
     * parseUl
     *   - Changes the <ul> tags to more semantical solution for ligght Exceptions
     *   
     * @param     string   $message  String to parse
     * @returns   string             The same string with more semantical solution for ligght Exceptions
     */
    protected function parseUl($message)
    {
        return implode(
            '<ul style="font-family: monospace; margin: 10px 20px;">',
            explode('<ul>', $message)
        );
    }
}
