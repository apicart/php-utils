<?php declare(strict_types = 1);

namespace Apicart\Utils\Tests\Sql;

use Apicart\Utils\Sql\QueryBuilder;
use PHPUnit\Framework\TestCase;

final class QueryBuilderTest extends TestCase
{

	/**
	 * @var QueryBuilder
	 */
	private $queryBuilder;


	protected function setUp(): void
	{
		parent::setUp();

		$this->queryBuilder = $this->createQueryBuilder();
	}


	public function testGetSql(): void
	{
		self::assertSame(
			'SELECT a.id, a.name, a.price' . PHP_EOL
			. 'FROM table a' . PHP_EOL
			. 'INNER JOIN inner_table b ON b.id = a.id' . PHP_EOL
			. 'LEFT JOIN left_table c ON c.id = b.id' . PHP_EOL
			. 'WHERE a.id IN ($1,$2,$3) AND (b.id = $4) OR ((c.id IS NULL OR c.id > $5)' . PHP_EOL,
			$this->queryBuilder->getSql()
		);
	}


	public function testGetParameters(): void
	{
		self::assertSame([
			'ids' => [1, 2, 3],
			'bId' => 'aaa',
			'minId' => 0,
		], $this->queryBuilder->getParameters());
	}


	private function createQueryBuilder(): QueryBuilder
	{
		return QueryBuilder::create()
			->select('a.id, a.name')
			->addSelect('a.price')
			->from('table', 'a')
			->innerJoin('inner_table', 'b', 'b.id = a.id')
			->leftJoin('left_table', 'c', 'c.id = b.id')
			->where('a.id IN (:ids)')
			->andWhere('b.id = :bId')
			->orWhere('(c.id IS NULL OR c.id > :minId')
			->setParameters([
				'ids' => [1, 2, 3],
				'bId' => 'aaa',
			])
			->setParameter('minId', 0);
	}

}
