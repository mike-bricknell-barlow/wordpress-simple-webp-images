<?php

namespace SWI\Includes;

class Quality
{
    public function get_quality(): int
    {
        return Options::get_quality() ?: 80;
    }
}