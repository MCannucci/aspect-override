<?php

namespace AspectOverride\Filters;

abstract class AbstractFilter extends \php_user_filter
{

    public abstract function getName(): string;

    public abstract function process(string $chunk, int $length);

    /**
     * Attaches the current filter to a stream.
     */
    public function register(): void
    {
        if (!\in_array($this->getName(), stream_get_filters(), true)) {
            $isRegistered = stream_filter_register($this->getName(), static::class);
        }
    }
    /**
     * Applies the current filter to a provided stream.
     *
     * @param resource $in
     * @param resource $out
     * @param int      $consumed
     * @param bool     $closing
     *
     * @return int PSFS_PASS_ON
     *
     * @see http://www.php.net/manual/en/php-user-filter.filter.php
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = $this->process($bucket->data, $bucket->datalen);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return \PSFS_PASS_ON;
    }
}