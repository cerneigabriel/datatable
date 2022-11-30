<?php

namespace Modules\DataTable\Core\Columns;

use Exception;
use Modules\DataTable\Core\Abstracts\DataTableColumn;

/**
 * Class TextColumn
 *
 * @package Modules\DataTable\Core\Columns
 */
class PriceColumn extends DataTableColumn
{
    /** @var string */
    public string $type = 'text';

    /** @var string|null */
    public ?string $currency_attribute;

    /** @var string|null */
    public ?string $currency = 'usd';

    /** @var bool */
    public bool $show_currency = true;

    /** @var string|null */
    public ?string $symbol_attribute;

    /** @var string|null */
    public ?string $symbol = '$';

    /** @var bool */
    public bool $show_symbol = true;

    /** @var string */
    public string $nullText = '0';

    /** @var int */
    public int $decimals = 2;

    /** @var string|null */
    public ?string $decimal_separator = '.';

    /** @var string|null */
    public ?string $thousands_separator = ',';


    /**
     * @param string $name
     * @param string|null $attribute
     * @param string $label
     */
    public function __construct(string $name, string $attribute = null, string $label = 'Price')
    {
        parent::__construct($name, $attribute, $label);
    }

    /**
     * @return $this
     */
    public function showOnlySymbol(): static
    {
        $this->show_currency = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function showOnlyCurrency(): static
    {
        $this->show_symbol = false;

        return $this;
    }

    /**
     * @param $currency
     * @return $this
     */
    public function setCurrency($currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @param $currency_attribute
     * @return $this
     */
    public function setCurrencyAttribute($currency_attribute): static
    {
        $this->currency_attribute = $currency_attribute;

        return $this;
    }

    /**
     * @param $symbol
     * @return $this
     */
    public function setSymbol($symbol): static
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * @param $symbol_attribute
     * @return $this
     */
    public function setSymbolAttribute($symbol_attribute): static
    {
        $this->symbol_attribute = $symbol_attribute;

        return $this;
    }

    /**
     * @param int $decimals
     * @param string|null $decimal_separator
     * @param string|null $thousands_separator
     * @return $this
     * @throws \Exception
     */
    public function setNumberFormat(int $decimals = 0, ?string $decimal_separator = '.', ?string $thousands_separator = ','): static
    {
        if ($decimals < 0) throw new Exception('Decimals must be greater than or equal to 0.');

        $this->decimals = $decimals;
        $this->decimal_separator = $decimal_separator;
        $this->thousands_separator = $thousands_separator;

        return $this;
    }

    /**
     * Resolve Data
     *
     * @param $data
     * @param $entity
     * @return string
     * @throws \Exception
     */
    public function resolveData($data, $entity): string
    {
        $data = $this->highlightStringInHtml($this->makePrice($data, $entity));

        if ($route = $this->getRoute($entity)) {
            return "<a href='$route' class='link-info'>$data</a>";
        }

        return $data;
    }

    /**
     * @param $data
     * @param $entity
     * @return string
     */
    protected function makePrice($data, $entity): string
    {
        $price = number_format((int)($data ?? $this->getNullText()), $this->decimals, $this->decimal_separator, $this->thousands_separator);

        if ($symbol = $this->symbol($entity)) {
            $price = strtoupper("{$symbol}{$price}");
        }

        if ($currency = $this->currency($entity)) {
            $price = strtoupper("$price $currency");
        }

        return $price;
    }

    /**
     * @param $entity
     * @return string|null
     */
    protected function symbol($entity): ?string
    {
        return $this->show_symbol ? strtoupper(isset($this->symbol_attribute) && isset($entity->{$this->symbol_attribute}) ? $entity->{$this->symbol_attribute} : $this->symbol) : null;
    }

    /**
     * @param $entity
     * @return string|null
     */
    protected function currency($entity): ?string
    {
        return $this->show_currency ? strtoupper(isset($this->currency_attribute) && isset($entity->{$this->currency_attribute}) ? $entity->{$this->currency_attribute} : $this->currency) : null;
    }
}