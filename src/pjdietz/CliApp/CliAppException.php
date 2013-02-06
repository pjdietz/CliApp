<?php

namespace pjdietz\CliApp;

use \Exception;

/**
 * Throw this exception (or a subclass) for any expected runtime errors.
 * Catch this in the run() method to send this error to the console.
 *
 * Example:
 *     $app = new EbookUtilApp();
 *
 *     try {
 *         $app->run();
 *     } catch (CLIAppException $e) {
 *         exit($e->getMessage() . "\n");
 *     }
 */
class CliAppException extends Exception
{
}
