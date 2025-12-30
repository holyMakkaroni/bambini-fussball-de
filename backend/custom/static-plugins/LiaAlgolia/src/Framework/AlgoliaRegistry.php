<?php declare(strict_types=1);

namespace Lia\Algolia\Framework;

class AlgoliaRegistry
{
    /**
     *
     * @param AbstractAlgoliaDefinition[] $definitions
     */
    public function __construct(private readonly iterable $definitions)
    {
    }

    /**
     * @return AbstractAlgoliaDefinition[]
     */
    public function getDefinitions(): iterable
    {
        return $this->definitions;
    }

    /**
     * @return iterable<string>
     */
    public function getDefinitionNames(): iterable
    {
        $names = [];

        foreach ($this->getDefinitions() as $definition) {
            $names[] = $definition->getEntityDefinition()->getEntityName();
        }

        return $names;
    }

    public function get(string $entityName): ?AbstractAlgoliaDefinition
    {
        foreach ($this->getDefinitions() as $definition) {
            if ($definition->getEntityDefinition()->getEntityName() === $entityName) {
                return $definition;
            }
        }

        return null;
    }

    public function has(string $entityName): bool
    {
        foreach ($this->getDefinitions() as $definition) {
            if ($definition->getEntityDefinition()->getEntityName() === $entityName) {
                return true;
            }
        }

        return false;
    }
}