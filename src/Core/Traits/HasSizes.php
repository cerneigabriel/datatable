<?php

namespace Modules\DataTable\Core\Traits;

trait HasSizes
{
    /** @var string|null */
    public ?string $min_width = null;

    /** @var string|null */
    public ?string $width = null;

    /** @var string|null */
    public ?string $max_width = null;

    /**
     * Get Column Width
     *
     * @return string|null
     */
    public function getWidth(): ?string
    {
        return $this->width;
    }

    /**
     * Set Column Width
     *
     * @param string|null $width
     *
     * @return $this
     */
    public function setWidth(?string $width): static
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Set Column Min Width
     *
     * @return string|null
     */
    public function getMinWidth(): ?string
    {
        return $this->min_width;
    }

    /**
     * Set Column Min Width
     *
     * @param string|null $min_width
     *
     * @return $this
     */
    public function setMinWidth(?string $min_width): static
    {
        $this->min_width = $min_width;

        return $this;
    }

    /**
     * Get Column Max Width
     *
     * @return string|null
     */
    public function getMaxWidth(): ?string
    {
        return $this->max_width;
    }

    /**
     * Set Column Max Width
     *
     * @param string|null $max_width
     *
     * @return $this
     */
    public function setMaxWidth(?string $max_width): static
    {
        $this->max_width = $max_width;

        return $this;
    }
}