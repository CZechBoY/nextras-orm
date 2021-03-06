<?php declare(strict_types = 1);

/**
 * This file is part of the Nextras\Orm library.
 * @license    MIT
 * @link       https://github.com/nextras/orm
 */

namespace Nextras\Orm\Relationships;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Traversable;
use function assert;
use function spl_object_hash;


class ManyHasMany extends HasMany
{
	public function getEntitiesForPersistence()
	{
		return $this->tracked + $this->toAdd + $this->toRemove;
	}


	public function doPersist()
	{
		if (!$this->isModified) {
			return;
		}

		$toRemove = [];
		foreach ($this->toRemove as $entity) {
			$id = $entity->getValue('id');
			$toRemove[$id] = $id;
		}
		$toAdd = [];
		foreach ($this->toAdd as $entity) {
			$id = $entity->getValue('id');
			$toAdd[$id] = $id;
		}

		$this->tracked += $this->toAdd;
		$this->toAdd = [];
		$this->toRemove = [];
		$this->isModified = false;
		$this->collection = null;
		if ($this->metadataRelationship->isMain) {
			$this->getRelationshipMapper()->remove($this->parent, $toRemove);
			$this->getRelationshipMapper()->add($this->parent, $toAdd);
		}

		$this->getRelationshipMapper()->clearCache();
		$this->relationshipMapper = null;
	}


	protected function modify(): void
	{
		$this->isModified = true;
	}


	protected function createCollection(): ICollection
	{
		/** @phpstan-var callable(Traversable<mixed,IEntity>):void $subscribeCb */
		$subscribeCb = function (Traversable $entities) {
			if (!$this->metadataRelationship->property) {
				return;
			}
			foreach ($entities as $entity) {
				$entity->getProperty($this->metadataRelationship->property)->trackEntity($this->parent);
				$this->trackEntity($entity);
			}
		};
		$mapper = $this->parent->getRepository()->getMapper();

		$collection = $this->getTargetRepository()->getMapper()->createCollectionManyHasMany($mapper, $this->metadata);
		$collection = $collection->setRelationshipParent($this->parent);
		$collection->subscribeOnEntityFetch($subscribeCb);
		return $this->applyDefaultOrder($collection);
	}


	protected function updateRelationshipAdd(IEntity $entity): void
	{
		if (!$this->metadataRelationship->property) {
			return;
		}

		$otherSide = $entity->getProperty($this->metadataRelationship->property);
		assert($otherSide instanceof ManyHasMany);
		$otherSide->collection = null;
		$otherSide->toAdd[spl_object_hash($this->parent)] = $this->parent;
		$otherSide->modify();
	}


	protected function updateRelationshipRemove(IEntity $entity): void
	{
		if (!$this->metadataRelationship->property) {
			return;
		}

		$otherSide = $entity->getProperty($this->metadataRelationship->property);
		assert($otherSide instanceof ManyHasMany);
		$otherSide->collection = null;
		$entityHash = spl_object_hash($this->parent);
		$otherSide->toRemove[$entityHash] = $this->parent;
		unset($otherSide->tracked[$entityHash]);
		$otherSide->modify();
	}
}
