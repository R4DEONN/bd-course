<?php
declare(strict_types=1);

namespace App\Repository;

use App\Common\Database\ConnectionProvider;
use App\Entity\Department;
use Exception;
use PDO;

readonly class DepartmentRepository
{
	private \App\Common\Database\Connection $connection;

	public function __construct()
	{
		$this->connection = ConnectionProvider::getConnection();
	}

	/**
	 * @return Department[]
	 * @throws Exception
	 */
	public function listAll(): array
	{
		$query = <<<SQL
			SELECT *
			FROM department
			ORDER BY department_id
			SQL;

		$departmentsData = $this->connection->execute($query)->fetchAll(PDO::FETCH_ASSOC);
		$departments = [];
		foreach ($departmentsData as $departmentData)
		{
			$departments[] = $this->hydrateDepartment($departmentData);
		}

		return $departments;
	}

	/**
	 * @throws Exception
	 */
	public function findById(int $id): ?Department
	{
		$query = <<<SQL
			SELECT
			    department_id,
			    city,
			    address
			FROM department
			WHERE department_id = :department_id
			SQL;

		$statement = $this->connection->execute($query, [
			'department_id' => $id,
		]);

		$row = $statement->fetch(PDO::FETCH_ASSOC);
		return $row ? new Department(
			$row['department_id'],
			$row['city'],
			$row['address'],
		) : null;
	}

	public function save(Department $department): int
	{
		$departmentId = $department->getId();
		if ($departmentId)
		{
			$this->updateDepartment($department);
		}
		else
		{
			$departmentId = $this->insertDepartment($department);
			$department->assignIdentifier($departmentId);
		}

		return $departmentId;
	}

	public function deleteById(int $id): void
	{
		$query = <<<SQL
			DELETE 
			FROM department
			WHERE department_id = :department_id
			SQL;

		$statement = $this->connection->execute($query, [
			'department_id' => $id,
		]);
	}

	/**
	 * @throws Exception
	 */
	private function hydrateDepartment(array $departmentData): Department
	{
		return new Department(
			$departmentData['department_id'],
			$departmentData['city'],
			$departmentData['address'],
		);
	}

	private function insertDepartment(Department $department): int
	{
		$query = <<<SQL
			INSERT INTO department
			    (city, address)
			VALUES 
			    (:city, :address)
			SQL;
		$this->connection->execute($query, [
			':city' => $department->getCity(),
			':address' => $department->getAddress(),
		]);

		return $this->connection->getLastInsertId();
	}

	private function updateDepartment(Department $department): void
	{
		$query = <<<SQL
            UPDATE department
            SET
              department.department_id = :department_id,
              city = :city,
              address = :address
            WHERE department_id = :department_id
            SQL;
		$params = [
			':department_id' => $department->getId(),
			':city' => $department->getCity(),
			':address' => $department->getAddress()
		];

		$stmt = $this->connection->execute($query, $params);
		if (!$stmt->rowCount())
		{
			throw new \RuntimeException("Optimistic lock failed for article {$department->getId()}");
		}
	}
}