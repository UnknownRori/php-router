<?php

namespace UnknownRori\Router\Traits;

/**
 * Auto implement default constraint method, it's need access to method where
 */
trait DefaultConstraintTrait
{
    /**
     * For easy typing on built in constraint
     *
     * @param  string|array $placeholder
     *
     * @return self
     */
    public function whereAlphaNum(string|array $placeholder): self
    {
        if (!is_array($placeholder)) {
            return $this->where([$placeholder => 'alphanum']);
        }

        foreach ($placeholder as $key => $value) {
            $this->where([$value => 'alphanum']);
        }

        return $this;
    }

    /**
     * For easy typing on built in constraint
     *
     * @param  string|array $placeholder
     *
     * @return self
     */
    public function whereNumeric(string|array $placeholder): self
    {
        if (!is_array($placeholder)) {
            return $this->where([$placeholder => 'numeric']);
        }

        foreach ($placeholder as $key => $value) {
            $this->where([$value => 'numeric']);
        }

        return $this;
    }

    /**
     * For easy typing on built in constraint
     *
     * @param  string|array $placeholder
     *
     * @return self
     */
    public function whereAlpha(string|array $placeholder): self
    {
        if (!is_array($placeholder)) {
            return $this->where([$placeholder => 'alpha']);
        }

        foreach ($placeholder as $key => $value) {
            $this->where([$value => 'alpha']);
        }

        return $this;
    }
}
