<?php

declare(strict_types=1);

namespace Core;

/**
 * HttpStatus enum - HTTP status codes with helper methods
 * 
 * Provides type-safe HTTP status codes and methods to check
 * response type (success, redirect, error, etc.)
 * 
 * @package Core
 * @author Light-MVC
 * @version 2.0.0
 */
enum HttpStatus: int
{
    // 2xx Success
    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NO_CONTENT = 204;

    // 3xx Redirection
    case MOVED_PERMANENTLY = 301;
    case FOUND = 302;
    case SEE_OTHER = 303;
    case NOT_MODIFIED = 304;
    case TEMPORARY_REDIRECT = 307;
    case PERMANENT_REDIRECT = 308;

    // 4xx Client Errors
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case NOT_ACCEPTABLE = 406;
    case REQUEST_TIMEOUT = 408;
    case CONFLICT = 409;
    case GONE = 410;
    case UNPROCESSABLE_ENTITY = 422;
    case TOO_MANY_REQUESTS = 429;

    // 5xx Server Errors
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case BAD_GATEWAY = 502;
    case SERVICE_UNAVAILABLE = 503;
    case GATEWAY_TIMEOUT = 504;

    /**
     * Check if status code is a success response (2xx)
     * 
     * @return bool True if status is 200-299
     */
    public function isSuccess(): bool
    {
        return $this->value >= 200 && $this->value < 300;
    }

    /**
     * Check if status code is a redirect response (3xx)
     * 
     * @return bool True if status is 300-399
     */
    public function isRedirect(): bool
    {
        return $this->value >= 300 && $this->value < 400;
    }

    /**
     * Check if status code is a client error (4xx)
     * 
     * @return bool True if status is 400-499
     */
    public function isClientError(): bool
    {
        return $this->value >= 400 && $this->value < 500;
    }

    /**
     * Check if status code is a server error (5xx)
     * 
     * @return bool True if status is 500-599
     */
    public function isServerError(): bool
    {
        return $this->value >= 500 && $this->value < 600;
    }

    /**
     * Check if status code is any error (4xx or 5xx)
     * 
     * @return bool True if status is 400 or higher
     */
    public function isError(): bool
    {
        return $this->value >= 400;
    }
}
