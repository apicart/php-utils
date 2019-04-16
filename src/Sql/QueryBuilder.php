<?php declare(strict_types = 1);

namespace Apicart\Utils\Sql;

final class QueryBuilder
{

	/**
	 * @var string|int|null
	 */
	private $offset;

	/**
	 * @var string|int|null
	 */
	private $limit;

	/**
	 * @var array
	 */
	private $parameters = [];

	/**
	 * @var string[]
	 */
	private $select = [];

	/**
	 * @var string[]
	 */
	private $join = [];

	/**
	 * @var string[]
	 */
	private $where = [];

	/**
	 * @var string[]
	 */
	private $groupBy = [];

	/**
	 * @var string[]
	 */
	private $orderBy = [];


	public static function create(): self
	{
		return new self;
	}


	public function select(string $columns): self
	{
		$this->select = [$columns];

		return $this;
	}


	public function addSelect(string $columns): self
	{
		$this->select[] = $columns;

		return $this;
	}


	public function from(string $tableName, string $alias = ''): self
	{
		$this->join[] = sprintf('FROM %s %s', $tableName, $alias);

		return $this;
	}


	public function addJoinDirectly(string $join): self
	{
		$this->join[] = $join;

		return $this;
	}


	public function join(string $tableName, string $alias, string $condition): self
	{
		return $this->innerJoin($tableName, $alias, $condition);
	}


	public function innerJoin(string $tableName, string $alias, string $condition): self
	{
		$this->join[$alias] = sprintf('INNER JOIN %s %s ON %s', $tableName, $alias, $condition);

		return $this;
	}


	public function leftJoin(string $tableName, string $alias, string $condition): self
	{
		$this->join[$alias] = sprintf('LEFT JOIN %s %s ON %s', $tableName, $alias, $condition);

		return $this;
	}


	public function addWhereDirectly(string $where): self
	{
		$this->where[] = $where;

		return $this;
	}


	public function where(string $condition): self
	{
		$this->where = [$condition];

		return $this;
	}


	public function andWhere(string $condition): self
	{
		$this->where[] = sprintf(' AND (%s)', $condition);

		return $this;
	}


	public function orWhere(string $condition): self
	{
		$this->where[] = sprintf(' OR (%s)', $condition);

		return $this;
	}


	public function groupBy(string $column): self
	{
		$this->groupBy = [$column];

		return $this;
	}


	public function addGroupBy(string $column): self
	{
		$this->groupBy[] = $column;

		return $this;
	}


	public function orderBy(string $column, string $order = 'DESC'): self
	{
		$this->orderBy = [$column . ' ' . $order];

		return $this;
	}


	public function addOrderBy(string $column, string $order = 'DESC'): self
	{
		$this->orderBy[] = $column . ' ' . $order;

		return $this;
	}


	/**
	 * @param string|int $limit
	 */
	public function setLimit($limit): self
	{
		$this->limit = $limit;

		return $this;
	}


	/**
	 * @param string|int $offset
	 */
	public function setOffset($offset): self
	{
		$this->offset = $offset;

		return $this;
	}


	public function setParameters(array $parameters): self
	{
		$this->parameters = $parameters;

		return $this;
	}


	/**
	 * @param int|string|bool|float $value
	 */
	public function setParameter(string $key, $value): self
	{
		$this->parameters[$key] = $value;

		return $this;
	}


	public function getSql(): string
	{
		$sql = 'SELECT ';

		// apply columns
		$sql .= implode(', ', $this->select);
		$sql .= PHP_EOL;

		// apply joins
		$sql .= implode(PHP_EOL, $this->join);
		$sql .= PHP_EOL;

		// apply conditions
		if ($this->where !== []) {
			$sql .= 'WHERE ';

			for ($index = 0; $index < count($this->where); $index++) {
				$condition = $this->where[$index];

				if ($index === 0) {
					$condition = preg_replace('/^ (AND|OR) /', '', $condition);
				}

				$sql .= $condition;
			}

			$sql .= PHP_EOL;
		}

		// apply group by
		if ($this->groupBy !== []) {
			$sql .= 'GROUP BY ';
			$sql .= implode(',', $this->groupBy);
			$sql .= PHP_EOL;
		}

		// apply orders
		if ($this->orderBy !== []) {
			$sql .= 'ORDER BY ';
			$sql .= implode(',', $this->orderBy);
			$sql .= PHP_EOL;
		}

		if ($this->limit) {
			$sql .= 'LIMIT ' . $this->limit;
			$sql .= PHP_EOL;
		}
		if ($this->offset) {
			$sql .= 'OFFSET ' . $this->offset;
		}

		return $this->replaceParameters($sql);
	}


	public function getParameters(): array
	{
		return $this->parameters;
	}


	/**
	 * @param mixed $default
	 * @return mixed
	 */
	public function getParameter(string $key, $default = null)
	{
		return $this->parameters[$key] ?? $default;
	}


	/**
	 * @return string[]
	 */
	public function getSelect(): array
	{
		return $this->select;
	}


	/**
	 * @return string[]
	 */
	public function getJoin(): array
	{
		return $this->join;
	}


	/**
	 * @return string[]
	 */
	public function getWhere(): array
	{
		return $this->where;
	}


	/**
	 * @return string[]
	 */
	public function getGroupBy(): array
	{
		return $this->groupBy;
	}


	/**
	 * @return string[]
	 */
	public function getOrderBy(): array
	{
		return $this->orderBy;
	}


	/**
	 * @return int|string|null
	 */
	public function getLimit()
	{
		return $this->limit;
	}


	/**
	 * @return int|string|null
	 */
	public function getOffset()
	{
		return $this->offset;
	}


	private function replaceParameters(string $sql): string
	{
		$userParameters = $this->getParameters();
		$newParameters = [];

		$parametrizedSql = preg_replace_callback(
			'#([ (]+):([a-zA-Z0-9]+)#',
			function ($match) use (&$newParameters, $userParameters) {
				$newParameter = $userParameters[$match[2]];

				// expand array parameter into string of parameters e.g. $1,$2,$3...
				if (is_array($newParameter)) {
					$output = $match[1];
					foreach ($newParameter as $data) {
						$newParameters[] = $data;
						$output .= sprintf('$%d,', count($newParameters));
					}
					$output = substr($output, 0, -1);

					return $output;
				}

				$newParameters[] = $newParameter;

				return sprintf('%s$%d', $match[1], count($newParameters));
			},
			$sql
		);

		$this->parameters = $newParameters;

		return $parametrizedSql;
	}

}
