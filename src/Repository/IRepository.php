<?php declare(strict_types = 1);

/**
 * This file is part of the Nextras\Orm library.
 * This file was inspired by PetrP's ORM library https://github.com/PetrP/Orm/.
 * @license    MIT
 * @link       https://github.com/nextras/orm
 */

namespace Nextras\Orm\Repository;

use Nextras\Orm\Collection\Functions\IArrayFunction;
use Nextras\Orm\Collection\Functions\IQueryBuilderFunction;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Entity\Reflection\EntityMetadata;
use Nextras\Orm\Mapper\IMapper;
use Nextras\Orm\Model\IModel;
use Nextras\Orm\NoResultException;


interface IRepository
{
	public function getModel(): IModel;


	public function setModel(IModel $model);


	public function getMapper(): IMapper;


	/**
	 * Hydrates entity.
	 */
	public function hydrateEntity(array $data): ?IEntity;


	/**
	 * Attaches entity to repository.
	 */
	public function attach(IEntity $entity);


	/**
	 * Detaches entity from repository.
	 */
	public function detach(IEntity $entity);


	/**
	 * Returns possible entity class names for current repository.
	 * @return string[]
	 * @phpstan-return (class-string<IEntity>)[]
	 */
	public static function getEntityClassNames(): array;


	/**
	 * Returns entity metadata.
	 * @param string|null $entityClass for STI (must extends base class)
	 */
	public function getEntityMetadata(string $entityClass = null): EntityMetadata;


	/**
	 * Returns entity class name.
	 * @phpstan-return class-string<IEntity>
	 */
	public function getEntityClassName(array $data): string;


	/**
	 * Returns IEntity filtered by conditions, null if none found.
	 */
	public function getBy(array $conds): ?IEntity;


	/**
	 * Returns IEntity filtered by conditions, throw if none found.
	 * @throws NoResultException
	 */
	public function getByChecked(array $conds): IEntity;


	/**
	 * Returns entity by primary value, null if none found.
	 * @param mixed $primaryValue
	 */
	public function getById($primaryValue): ?IEntity;


	/**
	 * Returns entity by primary value, throws if none found.
	 * @param mixed $primaryValue
	 * @throws NoResultException
	 */
	public function getByIdChecked($primaryValue): IEntity;


	/**
	 * Returns entity collection with all entities.
	 */
	public function findAll(): ICollection;


	/**
	 * Returns entity collection filtered by conditions.
	 */
	public function findBy(array $where): ICollection;


	/**
	 * Returns entities by primary values.
	 * @param mixed[] $primaryValues
	 */
	public function findById($primaryValues): ICollection;


	/**
	 * Returns collection functions instance.
	 * @return IArrayFunction|IQueryBuilderFunction
	 */
	public function getCollectionFunction(string $name);


	public function persist(IEntity $entity, bool $withCascade = true): IEntity;


	public function persistAndFlush(IEntity $entity, bool $withCascade = true): IEntity;


	public function remove(IEntity $entity, bool $withCascade = true): IEntity;


	public function removeAndFlush(IEntity $entity, bool $withCascade = true): IEntity;


	/**
	 * Flushes all persisted changes in all repositories.
	 */
	public function flush();


	/**
	 * DO NOT CALL THIS METHOD DIRECTLY.
	 * @internal
	 * @ignore
	 */
	public function doPersist(IEntity $entity);


	/**
	 * DO NOT CALL THIS METHOD DIRECTLY.
	 * @internal
	 * @ignore
	 */
	public function doRemove(IEntity $entity);


	/**
	 * DO NOT CALL THIS METHOD DIRECTLY.
	 * @internal
	 * @return array<array<IEntity>> Returns array where index 0 contains all persited, index 1 contains array of removed entities.
	 * The first key contains all flushed persisted entities.
	 * The second key contains all flushed removed entities.
	 * @ignore
	 */
	public function doFlush();


	/**
	 * DO NOT CALL THIS METHOD DIRECTLY.
	 * @internal
	 * @ignore
	 */
	public function doClear();


	/**
	 * DO NOT CALL THIS METHOD DIRECTLY.
	 * Fires the event on the entity.
	 * @internal
	 * @ignore
	 */
	public function doRefreshAll(bool $allowOverwrite): void;


	// === events ======================================================================================================


	/** @internal */
	public function onBeforePersist(IEntity $entity);


	/** @internal */
	public function onAfterPersist(IEntity $entity);


	/** @internal */
	public function onBeforeInsert(IEntity $entity);


	/** @internal */
	public function onAfterInsert(IEntity $entity);


	/** @internal */
	public function onBeforeUpdate(IEntity $entity);


	/** @internal */
	public function onAfterUpdate(IEntity $entity);


	/** @internal */
	public function onBeforeRemove(IEntity $entity);


	/** @internal */
	public function onAfterRemove(IEntity $entity);
}
