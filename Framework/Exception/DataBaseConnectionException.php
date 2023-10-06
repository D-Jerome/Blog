<?php
	namespace Framework\Exception;

use Exception;

    class DataBaseConnectionException extends Exception
	{
		public function __construct($message = "database connection doesn't work")
		{
			parent::__construct($message, "0000");
		}
	}