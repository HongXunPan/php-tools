<?php /** @noinspection PhpCSValidationInspection */

namespace HongXunPan\Tools\TimeLimitOffers;

use Exception;

class TimeLimitOffersException extends Exception
{
    const NO_CHANCE_LEFT = 1;
    const OUT_OF_LIMIT = 2;

    public function __construct($code = self::NO_CHANCE_LEFT, $message = "", $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
