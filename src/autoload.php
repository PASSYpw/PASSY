<?php
/*!
 *    PASSY - Modern HTML5 Password Manager
 *    Copyright (C) 2017 Sefa Eyeoglu <contact@scrumplex.net> (https://scrumplex.net)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

function passy_autoload($class)
{
	$path = realpath(__DIR__) . '/' . str_replace('\\', '/', $class) . '.php';
	if (is_readable($path))
		require_once $path;
}

spl_autoload_register('passy_autoload');
