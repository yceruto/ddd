<?php

declare(strict_types=1);

namespace OpenSolid\Ddd\Domain\Aggregate;

use OpenSolid\Ddd\Domain\Error\DomainError;
use OpenSolid\DomainEvent\DomainEvent;

trait AggregateRoot
{
    private array $domainEvents = [];
    private array $exceptions = [];

    /**
     * @return array<DomainEvent>
     */
    final public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return array_values($domainEvents);
    }

    final protected function pushDomainEvent(DomainEvent $domainEvent): void
    {
        if (array_key_exists($domainEvent::class, $this->domainEvents)) {
            return;
        }

        $this->domainEvents[$domainEvent::class] = $domainEvent;
    }

    final protected function pushDomainError(string|DomainError $error): void
    {
        $this->exceptions[] = is_string($error) ? DomainError::create($error) : $error;
    }

    final protected function throwDomainErrors(): void
    {
        if ([] === $this->exceptions) {
            return;
        }

        if (1 === count($this->exceptions)) {
            throw $this->exceptions[0];
        }

        throw DomainError::createMany($this->exceptions);
    }
}
