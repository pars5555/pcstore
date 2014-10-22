<?php

/**
 * Contains definitions for all participant roles in system.
 * 
 * @author Vahagn Sookiasian, Yerem Khalatyan
 * @package users
 */
class UserGroups {

	/**
	 * @var System administrator
	 */
	public static $ADMIN = 0;

	/**
	 * Registered user	 
	 */
	public static $USER = 1;

	/**
	 * @var Affiliate administrator
	 */
	public static $COMPANY = 3;

	public static $SERVICE_COMPANY = 4;

	/**
	 * @var Non authorized user with minimum privileges
	 */
	public static $GUEST = 2;

}

?>