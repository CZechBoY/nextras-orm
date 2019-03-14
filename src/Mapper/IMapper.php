<?php declare(strict_types = 1);

/**
 * This file is part of the Nextras\Orm library.
 * @license    MIT
 * @link       https://github.com/nextras/orm
 */

namespace Nextras\Orm\Mapper;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Entity\Reflection\PropertyMetadata;
use Nextras\Orm\Repository\IRepository;


interface IMapper
{
	/**
	 * Returns all entities.
	 */
	public function findAll(): ICollection;


	/**
	 * Creates collection with HasOne mapper.
	 */
	public function createCollectionManyHasOne(PropertyMetadata $metadata): ICollection;


	/**
	 * Creates collection with OneHasOneDirected mapper.
	 */
	public function createCollectionOneHasOne(PropertyMetadata $metadata): ICollection;


	/**
	 * Creates collection with ManyHasMany mapper.
	 */
	public function createCollectionManyHasMany(IMapper $mapper, PropertyMetadata $metadata): ICollection;


	/**
	 * Creates collection with OneHasMany mapper.
	 */
	public function createCollectionOneHasMany(PropertyMetadata $metadata): ICollection;


	public function setRepository(IRepository $repository): void;


	public function getRepository(): IRepository;


	/**
	 * @see IRepository::persist()
	 * @return mixed
	 */
	public function persist(IEntity $entity);


	/**
	 * @see IRepository::remove()
	 * @return void
	 */
	public function remove(IEntity $entity);


	/**
	 * @see IRepository::flush()
	 */
	public function flush(): void;


	/**
	 * Clears cache object for collection.
	 * @internal
	 * @return void
	 */
	public function clearCache();
}
