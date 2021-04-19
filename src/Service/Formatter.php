<?php

class Formatter
{
    /**
     * @param Quote $quote
     *
     * @return string
     */
    public static function renderHtml(Quote $quote)
    {
        return '<p>' . $quote->id . '</p>';
    }

    /**
     * @param Quote $quote
     *
     * @return string
     */
    public static function renderText(Quote $quote)
    {
        return (string) $quote->id;
    }
}
