<?php

/**
 * Finally, A light, permissions-checking logging class.
 *
 * Author	: Kenneth Katzgrau < katzgrau@gmail.com >
 * Date	: July 26, 2008
 * Comments	: Originally written for use with wpSearch
 * Website	: http://codefury.net
 * Version	: 1.0
 *
 * Usage:
 *      $log = new KLogger ("log.txt" , KLogger::INFO);
 *      $log->logInfo("Returned a million search results"); //Prints to the log file
 *      $log->logFatal("Oh dear.");                         //Prints to the log file
 *      $log->logDebug("x = 5");                            //Prints nothing due to priority setting
 */
class KLogger {

	const TRACE     = 0;
	const DEBUG 	= 1;	// Most Verbose
	const INFO 		= 2;	// ...
	const WARN 		= 3;	// ...
	const ERROR 	= 4;	// ...
	const FATAL 	= 5;	// Least Verbose
	const OFF 		= 6;	// Nothing at all.

	const LOG_OPEN 		= 1;
	const OPEN_FAILED 	= 2;
	const LOG_CLOSED 	= 3;

	/* Public members: Not so much of an example of encapsulation, but that's okay. */
	public $log_Status 	= KLogger::LOG_CLOSED;
	public $DateFormat	= DateTime::W3C;
	public $MessageQueue;

	private $log_file;
	private $priority = KLogger::INFO;

	private $file_handle;

	public function __construct($filepath , $priority) {

		if ($priority == KLogger::OFF) return;

		$this->log_file = $filepath;
		$this->MessageQueue = array();
		$this->priority = $priority;

		if (file_exists($this->log_file)) {
			if (!is_writable($this->log_file)) {
				$this->log_Status = KLogger::OPEN_FAILED;
				$this->MessageQueue[] = "The file exists, but could not be opened for writing. Check that appropriate permissions have been set.";
				return;
			}
		}

		if ($this->file_handle = fopen($this->log_file , "a")) {
			$this->log_Status = KLogger::LOG_OPEN;
			$this->MessageQueue[] = "The log file was opened successfully.";
		} else {
			$this->log_Status = KLogger::OPEN_FAILED;
			$this->MessageQueue[] = "The file could not be opened. Check permissions.";
		}

		return;
	}

	public function __destruct() {
		if ($this->file_handle) {
			fclose($this->file_handle);
		}
	}

	public function logInfo($line) {
		$this->log($line, KLogger::INFO);
	}

	public function logDebug($line) {
		$this->log($line , KLogger::DEBUG);
	}

	public function logWarn($line) {
		$this->log($line , KLogger::WARN);
	}

	public function logError($line) {
		$this->log($line , KLogger::ERROR);
	}

	public function logFatal($line) {
		$this->log($line , KLogger::FATAL);
	}

	public function logTrace($line) {
		$this->log($line , KLogger::TRACE);
	}

	public function log($line, $priority) {
		if ($this->priority <= $priority) {
			$status = $this->getTimeLine($priority);
			$this->writeLine("$status $line \n");
		}
	}

	public function writeLine($line) {
		if ($this->log_Status == KLogger::LOG_OPEN && $this->priority != KLogger::OFF) {
			if (fwrite($this->file_handle, $line) === false) {
				$this->MessageQueue[] = "The file could not be written to. Check that appropriate permissions have been set.";
			}
		}
	}

	private function getTimeLine($level) {
		$time = date($this->DateFormat);
		switch($level) {
			case KLogger::TRACE:
				return "$time - TRACE -->";
			case KLogger::INFO:
				return "$time - INFO  -->";
			case KLogger::WARN:
				return "$time - WARN  -->";
			case KLogger::DEBUG:
				return "$time - DEBUG -->";
			case KLogger::ERROR:
				return "$time - ERROR -->";
			case KLogger::FATAL:
				return "$time - FATAL -->";
			default:
				return "$time - LOG   -->";
		}
	}
}
