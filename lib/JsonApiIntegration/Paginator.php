<?php

namespace Argonauts\JsonApiIntegration;

class Paginator
{
    public function __construct($items, $total, $offset, $limit)
    {
        $this->items = $items;
        $this->total = $total;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function getFirstPageOffsetAndLimit()
    {
        if ($this->limit === 0) {
            return [0, 0];
        }

        return [0, $this->limit];
    }

    public function getLastPageOffsetAndLimit()
    {
        if ($this->limit === 0) {
            return [0, 0];
        }
        $last = max(0, floor($this->total / $this->limit - 1) * $this->limit);

        return [$last, $this->limit];
    }

    public function getPrevPageOffsetAndLimit()
    {
        if ($this->limit === 0) {
            return null;
        }

        if ($this->offset - $this->limit < 0) {
            return null;
        }

        return [max(0, $this->offset - $this->limit), $this->limit];
    }

    public function getNextPageOffsetAndLimit()
    {
        if ($this->limit === 0) {
            return null;
        }

        if ($this->total <= $this->offset + $this->limit) {
            return null;
        }

        return [$this->offset + $this->limit, $this->limit];
    }
}
