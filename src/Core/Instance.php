<?php

namespace AspectOverride\Core;

class Instance
{
    /** @var Configuration */
    protected $config;
    /** @var StreamInterceptor */
    protected $interceptor;
    /** @var array<string,callable> */
    protected $autoloaderFiles = [];
    /** @var bool */
    protected $hasNeverBeenInitalized = true;

    public function __construct(
        StreamInterceptor $interceptor = null
    )
    {
        $this->interceptor = $interceptor ?? new StreamInterceptor();
    }

    public function initialize(Configuration $configuration): void
    {
        $this->config = $configuration;
        if($this->hasNeverBeenInitalized) {
            $this->hasNeverBeenInitalized = false;
            $this->interceptor->restore();
            $this->interceptor->enable();
        }
    }

    public function getConfiguration(): Configuration
    {
        return $this->config;
    }
}
