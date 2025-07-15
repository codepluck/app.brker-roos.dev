<?php

// Author: Neeraj Saini
// Email: hax-neeraj@outlook.com
// GitHub: https://github.com/haxneeraj/
// LinkedIn: https://www.linkedin.com/in/hax-neeraj/

namespace Modules\Abstracts\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException as coreModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as coreNotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException as coreHttpException;
use PDOException as corePDOException;



use App\Exceptions\Model\ModelNotFoundException;
use App\Exceptions\NotFoundHttpException;
use App\Exceptions\HttpException;
use App\Exceptions\PDOException;

/**
 * Class ExceptionHandler
 * 
 * A basic exception handler class that provides a method for handling exceptions.
 * This class can be used to customize how exceptions are handled and reported within
 * the application.
 */
class ExceptionHandler
{
    /**
     * Handles an exception thrown during the application's execution.
     *
     * This method is designed to be a catch-all for exceptions, allowing custom
     * logic to be applied when an exception occurs. In this basic implementation,
     * the exception details are dumped to the screen using Laravel's `dd()` function.
     *
     * @param \Throwable $e The exception that was thrown.
     * @return void
     */
    public function handle(\Throwable $e)
    {
        if(config('app.dev_mode')):
            return dd($e);
        endif;

        return match (true) {
            $e instanceof coreModelNotFoundException => (new ModelNotFoundException($e))->render(),
            $e instanceof coreNotFoundHttpException => (new NotFoundHttpException($e))->render(),
            $e instanceof coreHttpException => (new HttpException($e))->render(),
            $e instanceof corePDOException => (new PDOException($e))->render(),
            default => $e,
        };
    }
}
