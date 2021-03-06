<?php declare(strict_types = 1);

/**
 * This file is part of the Nextras\Orm library.
 * @license    MIT
 * @link       https://github.com/nextras/orm
 */

namespace Nextras\Orm\Collection;


class MutableArrayCollection extends ArrayCollection
{
	/**
	 * @phpstan-param array<\Nextras\Orm\Entity\IEntity> $data
	 * @return static
	 */
	public function withData(array $data): ICollection
	{
		$collection = clone $this;
		$collection->data = $data;
		return $collection;
	}
}
