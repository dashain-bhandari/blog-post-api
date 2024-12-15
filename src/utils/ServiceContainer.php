<?php

declare(strict_types=1);

class ServiceContainer
{
    private array $services = [];

    public function register(string $name, callable $resolver): void
    {
        $this->services[$name] = $resolver;
    }

    public function get(string $name)
    {
        if (!isset($this->services[$name])) {
            throw new Exception("Service {$name} not registered");
        }

        return $this->services[$name]();
    }
}
