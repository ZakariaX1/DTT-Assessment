<?php

namespace App\Plugins\Http\Response;

use App\Plugins\Http\JsonStatus;

// For use when an unfinished/unimplemented method is called

class MethodNotAllowed extends JsonStatus {
    /** @var int */
    const STATUS_CODE = 405;
    /** @var string */
    const STATUS_MESSAGE = 'Method Not Allowed';

    /**
     * Constructor of this class
     * @param mixed $body
     */
    public function __construct($body = '') {
        parent::__construct(self::STATUS_CODE, self::STATUS_MESSAGE, $body);
    }
}
