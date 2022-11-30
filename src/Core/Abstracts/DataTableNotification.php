<?php

namespace Modules\DataTable\Core\Abstracts;

use Exception;

class DataTableNotification
{
    public const WARNING = 'warning';
    public const SUCCESS = 'success';
    public const TYPES = [
        self::SUCCESS,
        self::WARNING,
    ];

    public const DEFAULT_TYPE = self::SUCCESS;
    public const DEFAULT_TIMEOUT = 3000;

    /** @var int */
    public int $level = 0;

    /** @var string */
    public string $type = self::DEFAULT_TYPE;

    /** @var int */
    public int $timeout = self::DEFAULT_TIMEOUT;

    /** @var string */
    public string $message;

    /**
     * @param string $type
     * @param string $message
     * @param int $timeout
     * @param int $level
     * @throws \Throwable
     */
    public function __construct(string $type = self::DEFAULT_TYPE, string $message = '', int $timeout = self::DEFAULT_TIMEOUT, int $level = 0)
    {
        throw_if(!in_array($type, self::TYPES), new Exception("Notification type is wrong. Got '$type', but expected one of these types: '" . implode("', '", self::TYPES) . "'."));
        throw_if($timeout <= 0, new Exception('Notification timeout should be greater than zero.'));
        throw_if(empty(trim($message)), new Exception('Notification message should not be empty.'));

        $this->message = $message;
        $this->type = $type;
        $this->timeout = $timeout;
        $this->level = $level;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     * @return $this
     */
    public function setTimeout(int $timeout): static
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * @param int $level
     * @return $this
     */
    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }
}