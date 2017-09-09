<?php

namespace PASSY;

/**
 * Class PASSY
 *
 * This class holds the instances of several backend related classes.
 *
 * @author Sefa Eyeoglu <contact@scrumplex.net>
 * @package PASSY
 */
class PASSY
{
	/**
	 * @var Database
	 */
	public static $db;

	/**
	 * @var IPLog
	 */
	public static $ipLog;

	/**
	 * @var Passwords
	 */
	public static $passwords;

	/**
	 * @var Tasks
	 */
	public static $tasks;

	/**
	 * @var TwoFactor
	 */
	public static $twoFactor;

	/**
	 * @var UserManager
	 */
	public static $userManager;

}
