<?php

namespace Modules\Abstracts\Exceptions;

use Exception as BaseException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

/**
 * Abstract base exception class for handling custom exceptions in the application.
 * 
 * This class extends the base Exception class and adds additional functionality
 * specific to the application's needs, including environment-specific messages and
 * custom status codes.
 */
abstract class Exception extends BaseException
{
    /**
     * The environment in which the application is running (e.g., local, production).
     *
     * @var string
     */
    protected string $environment;

    /**
     * An array to store any additional errors or details related to the exception.
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * Constructor for the custom exception class.
     *
     * Initializes the exception with a message and code, and sets the environment
     * based on the application's configuration.
     *
     * @param string|null $message The exception message.
     * @param int|null $code The exception code.
     * @param \Throwable|null $previous The previous throwable used for the exception chaining.
     */
    public function __construct(string|null $message = null, int|null $code = null, \Throwable|null $previous = null)
    {
        // Set the environment from the application's configuration.
        $this->environment = Config::get('app.env');

        // Call the parent constructor with the processed message and status code.
        parent::__construct($this->message($message), $this->status($code), $previous);
    }

    /**
     * Determines the message to be used for the exception.
     *
     * If a custom message is provided, it is used; otherwise, the default message is used.
     *
     * @param string|null $message The custom message to use.
     * @return string The final message for the exception.
     */
    private function message(string|null $message = null): string
    {
        return is_null($message) ? $this->message : $message;
    }

    /**
     * Determines the status code for the exception.
     *
     * If a custom code is provided, it is used; otherwise, the default code is used.
     *
     * @param int|null $code The custom status code to use.
     * @return int The final status code for the exception.
     */
    private function status(int|null $code = null): int
    {
        return is_null($code) ? $this->code : $code;
    }
}