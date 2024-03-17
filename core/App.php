<?php

namespace core;

class App extends Container
{
    public function __construct(
        public string $base_path,
    )
    {
    }
}