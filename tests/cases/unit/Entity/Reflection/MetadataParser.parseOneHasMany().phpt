<?php declare(strict_types = 1);

/**
 * @testCase
 */

namespace NextrasTests\Orm\Entity\Reflection;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Entity\Reflection\MetadataParser;
use Nextras\Orm\Entity\Reflection\PropertyMetadata;
use Nextras\Orm\Entity\Reflection\PropertyRelationshipMetadata;
use Nextras\Orm\Relationships\OneHasMany;
use Nextras\Orm\Repository\Repository;
use NextrasTests\Orm\TestCase;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../../../bootstrap.php';


/**
 * @property ?int $id {primary}
 * @property mixed $test1 {1:m Bar::$property}
 * @property mixed $test2 {1:m Bar::$property, orderBy=entity->id}
 * @property mixed $test3 {1:m Bar::$property, orderBy=[id,DESC]}
 * @property mixed $test4 {1:m Bar::$property, orderBy=[id=DESC, entity->id=ASC]}
 * @property OneHasMany&object[] $test5 {1:m Bar::$property}
 */
class OneHasManyTestEntity extends Entity
{}


class BarRepository extends Repository
{
	public static function getEntityClassNames(): array
	{
		return [ManyHasManyTestEntity::class];
	}
}


class MetadataParserParseOneHasManyTest extends TestCase
{
	public function testOneHasMany()
	{
		$dependencies = [];
		$parser = new MetadataParser([
			Bar::class => BarRepository::class,
		]);

		$metadata = $parser->parseMetadata(OneHasManyTestEntity::class, $dependencies);

		/** @var PropertyMetadata $propertyMeta */
		$propertyMeta = $metadata->getProperty('id');
		Assert::same(['int' => true], $propertyMeta->types);
		Assert::true($propertyMeta->isNullable);

		$propertyMeta = $metadata->getProperty('test1');
		Assert::same(BarRepository::class, $propertyMeta->relationship->repository);
		Assert::same('property', $propertyMeta->relationship->property);
		Assert::same(NULL, $propertyMeta->relationship->order);
		Assert::same(PropertyRelationshipMetadata::ONE_HAS_MANY, $propertyMeta->relationship->type);

		$propertyMeta = $metadata->getProperty('test2');
		Assert::same(BarRepository::class, $propertyMeta->relationship->repository);
		Assert::same('property', $propertyMeta->relationship->property);
		Assert::same(['entity->id' => ICollection::ASC], $propertyMeta->relationship->order);
		Assert::same(PropertyRelationshipMetadata::ONE_HAS_MANY, $propertyMeta->relationship->type);

		$propertyMeta = $metadata->getProperty('test3');
		Assert::same(BarRepository::class, $propertyMeta->relationship->repository);
		Assert::same('property', $propertyMeta->relationship->property);
		Assert::same(['id' => ICollection::DESC], $propertyMeta->relationship->order);
		Assert::same(PropertyRelationshipMetadata::ONE_HAS_MANY, $propertyMeta->relationship->type);

		$propertyMeta = $metadata->getProperty('test4');
		Assert::same(BarRepository::class, $propertyMeta->relationship->repository);
		Assert::same('property', $propertyMeta->relationship->property);
		Assert::same(['id' => ICollection::DESC, 'entity->id' => ICollection::ASC], $propertyMeta->relationship->order);
		Assert::same(PropertyRelationshipMetadata::ONE_HAS_MANY, $propertyMeta->relationship->type);
		Assert::same(['mixed' => true], $propertyMeta->types);

		$propertyMeta = $metadata->getProperty('test5');
		Assert::same([OneHasMany::class => true, 'array' => true], $propertyMeta->types);
	}
}


$test = new MetadataParserParseOneHasManyTest($dic);
$test->run();
