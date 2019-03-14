<?php declare(strict_types = 1);

/**
 * This file is part of the Nextras\Orm library.
 * @license    MIT
 * @link       https://github.com/nextras/orm
 */

namespace Nextras\Orm\Mapper;

use Countable;
use Iterator;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;


interface IRelationshipMapper
{
	/**
	 * Returns iterator.
	 * @return Countable&Iterator
	 */
	public function getIterator(IEntity $parent, ICollection $collection): Iterator;


	/**
	 * Returns iterator's counts.
	 */
	public function getIteratorCount(IEntity $parent, ICollection $collection): int;
}
