<?php
	namespace Framework\Exception;

use Exception;

	class PropertyNotFoundException extends Exception
	{
		public function __construct($message = "Property has not been found")
		{
			parent::__construct($message, "0003");
		}
	}