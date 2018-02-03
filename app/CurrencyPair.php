<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrencyPair extends Model
{
    /**
     * @var string
     */
    public $from;

    /**
     * @var string
     */
    public $to;

    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function __toString()
    {
        return "{$this->from}_{$this->to}";
    }

    public static function toObject(string $pair)
    {
        $pair = explode('_', $pair);

        return new self($pair[0], $pair[1]);
    }
}
