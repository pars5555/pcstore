<?php

/**
 * Contains request groups definitions.
 * @author Vahagn Sookiasian, Yerem Khalatyan
 */
class RequestGroups {

	public static $adminRequest = 0;
	public static $userRequest = 2;
	public static $companyRequest = 4;
	public static $serviceCompanyRequest = 4;
	public static $companyAndServiceCompanyRequest = 5;
	public static $userCompanyRequest = 10;
	public static $guestRequest = 14;

	/**
	 * @var request type used for system requests only
	 */
	public static $systemRequest = 16;

}

?>