<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;
use PDOStatement;

class Connection
{
	static private ?PDO $handle = null;

	public function __construct(string $dsn, string $user, string $password)
	{
		if (self::$handle === null)
		{
			self::$handle = new PDO($dsn, $user, $password);
		}
	}

	public function execute(string $sql, array $params): PDOStatement
	{
		$statement = self::$handle->prepare($sql);
		$statement->execute($params);
		return $statement;
	}

	public function getLastInsertId(): int
	{
		return (int)self::$handle->lastInsertId();
	}
}