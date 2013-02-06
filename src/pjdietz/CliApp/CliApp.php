<?php

namespace pjdietz\CliApp;

/**
 * Abstract base class for command line interface applications.
 *
 * Defines some functionality for reading options and sending messages.
 */
abstract class CliApp
{
    const VERBOSITY_VERBOSE = 1;
    const VERBOSITY_NORMAL = 0;
    const VERBOSITY_SILENT = -1;

    protected $debugMode = false;
    protected $debugPattern = '[DEBUG] %s';
    protected $optsShort;
    protected $optsLong;
    protected $options;
    protected $stdout;
    protected $verbosityMessageDefault = self::VERBOSITY_NORMAL;
    protected $verbosity = self::VERBOSITY_NORMAL;

    /**
     * Create a new instance of the application
     */
    public function __construct()
    {
        $this->stdout = fopen('php://stdout', 'w');
        $this->optsShort = '';
        $this->optsLong = array();
    }

    public function __destruct()
    {
        fclose($this->stdout);
    }

    /**
     * Start the application. A CLI script may simply instantiate an app and
     * call it's run method, passing no parameters (indicating the app should
     * read the options from the command line invocation). Or, an app may be
     * instantiated from within another application and passed an array of
     * options to run as a sub program.
     *
     * @param array $options
     * @return mixed  The status code to return after execution.
     */
    public function run($options = null)
    {
        // Prepare the instance.
        if (is_null($options)) {

            // If no options were passed, read the command line options.
            $this->options = getopt($this->optsShort, $this->optsLong);

        } elseif (is_array($options)) {

            // If it's an array, assume it corresponds to the result of a
            // getopt() call.
            $this->options = $options;

        } else {

            // Otherwise, assume no options.
            $this->options = array();

        }

        $this->readOpts();
        return $this->main();

    }

    /**
     * Write this message to the standard out.
     *
     * @param string $message
     */
    protected function messageWrite($message)
    {
        fwrite($this->stdout, $message);
    }

    /**
     * Write the message to the standard out as long as the application is
     * running in a high-enough verbosity mode.
     *
     * @param string $message
     * @param int $messageVerbosity Level of verbosity for the message
     */
    protected function message($message, $messageVerbosity = null)
    {
        // Check if the caller supplied a verbosity level for the message.
        // If not, assume the application default.
        if (is_null($messageVerbosity)) {
            $messageVerbosity = $this->verbosityMessageDefault;
        }

        // If this message's verbosity level is at least as high as the
        // applcation verbosity level, display the message.
        if ($this->verbosity >= $messageVerbosity) {
            $this->messageWrite($message);
        }
    }

    /**
     * Write a message, prefix with the debug prefix, to the STD out.
     * Only do this if the application is running in debug mode.
     *
     * @param string $message
     */
    protected function debugMessage($message)
    {
        if ($this->debugMode) {
            $this->messageWrite(sprintf($this->debugPattern, $message));
        }
    }

    /**
     * Read the options instance and set other members accordingling.
     */
    abstract protected function readOpts();

    /**
     * This is the main execution function for the app. Called after readOpts().
     *
     * @return mixed The status code to return after execution.
     */
    abstract protected function main();

}
