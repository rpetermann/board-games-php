<?php

namespace App\Exception;

use Exception;

/**
 * AbstractException
 */
abstract class AbstractException extends Exception
{
    /**
     * @var array
     */
    protected $codeMap = [];

    /**
     * __construct
     *
     * @param string $message
     * @param integer|null $code
     */
    public function __construct(string $message, ?int $code = null)
    {
        $this->populateCodeMap();
        $code = $code ?? $this->codeMap[$message];

        parent::__construct($message, $code ?? 400);
    }

    /**
     * populateCodeMap
     *
     * @return void
     */
    abstract protected function populateCodeMap(): void;
}
