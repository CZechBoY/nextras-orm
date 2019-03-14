<?php declare(strict_types = 1);

/**
 * This file is part of the Nextras\Orm library.
 * @license    MIT
 * @link       https://github.com/nextras/orm
 */

namespace Nextras\Orm\Collection;

use Countable;
use Iterator;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Entity\IEntityHasPreloadContainer;


class MultiEntityIterator implements IEntityPreloadContainer, Iterator, Countable
{
	/** @var int */
	private $position = 0;

	/** @var array<int|string, IEntity[]> */
	private $data;

	/** @var IEntity[] */
	private $iteratable;

	/** @var array */
	private $preloadCache;


	/**
	 * @param array<int|string, IEntity[]> $data
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}


	/**
	 * @param string|int $index
	 */
	public function setDataIndex($index): void
	{
		if (!isset($this->data[$index])) {
			$this->data[$index] = [];
		}
		$this->iteratable = & $this->data[$index];
		$this->rewind();
	}


	public function next(): void
	{
		++$this->position;
	}


	public function current(): ?IEntity
	{
		if (!isset($this->iteratable[$this->position])) {
			return null;
		}

		$current = $this->iteratable[$this->position];
		if ($current instanceof IEntityHasPreloadContainer) {
			$current->setPreloadContainer($this);
		}
		return $current;
	}


	public function key(): int
	{
		return $this->position;
	}


	public function valid(): bool
	{
		return isset($this->iteratable[$this->position]);
	}


	public function rewind(): void
	{
		$this->position = 0;
	}


	public function count(): int
	{
		return count($this->iteratable);
	}


	public function getPreloadValues(string $property): array
	{
		if (isset($this->preloadCache[$property])) {
			return $this->preloadCache[$property];
		}

		$values = [];
		foreach ($this->data as $entities) {
			foreach ($entities as $entity) {
				// property may not exist when using STI
				if ($entity->getMetadata()->hasProperty($property)) {
					$values[] = $entity->getRawValue($property);
				}
			}
		}

		return $this->preloadCache[$property] = $values;
	}
}
