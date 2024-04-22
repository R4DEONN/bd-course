<?php

namespace App\Repository;

use App\Entity\Department;
use App\Entity\Worker;
use Exception;
use PDO;

readonly class DepartmentRepository
{
	private PDO $connection;

	public function __construct()
	{
		$this->connection = new PDO(
			$_ENV['APP_DATABASE_DSN'],
			$_ENV['DATABASE_USER'],
			$_ENV['DATABASE_PASSWORD'],
		);
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

		$departments_assoc = $this->connection->query($query)->fetchAll(PDO::FETCH_ASSOC);
		$departments = [];
		foreach ($departments_assoc as $department_assoc) {
			$workers = $this->findWorkersByDepartmentId($department_assoc['department_id']);

			$departments[] = new Department(
				$department_assoc['department_id'],
				$department_assoc['city'],
				$department_assoc['address'],
				$workers,
			);
		}

		return $departments;
	}

	/**
	 * @param int $id
	 * @return Worker[]
	 * @throws Exception
	 */
	private function findWorkersByDepartmentId(int $id): array
	{
		$workers = [];

		$query = <<<SQL
				SELECT *
				FROM worker
				where department_id = :department_id
				SQL;
		$statement = $this->connection->prepare($query);
		$statement->execute([
			'department_id' => $id,
		]);

		$workers_assoc = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($workers_assoc as $worker) {
			$workers[] = new Worker(
				$worker['worker_id'],
				$worker['full_name'],
				$worker['job_title'],
				$worker['phone'],
				$worker['email'],
				$worker['gender'],
				new \DateTimeImmutable($worker['birth_date']),
				new \DateTimeImmutable($worker['hire_date']),
				$worker['description'],
			);
		}

		return $workers;
	}

	public function findById(int $id): ?Department
	{
		$query = <<<SQL
			SELECT *
			FROM department
			WHERE department_id = :department_id
			SQL;

		$statement = $this->connection->prepare($query);
		$statement->execute([
			'department_id' => $id
		]);

		$department = $statement->fetch(PDO::FETCH_ASSOC);
		return $department ? new Department(
			$department['department_id'],
			$department['city'],
			$department['address'],
			$this->findWorkersByDepartmentId($id),
		) : null;
	}

	public function add(Department $department): int
	{
		$query = <<<SQL
			INSERT INTO department
			    (city, address)
			VALUES 
			    (:city, :address)
			SQL;
		$statement = $this->connection->prepare($query);
		$statement->execute([
			':city' => $department->getCity(),
			':address' => $department->getAddress(),
		]);

		return (int)$this->connection->lastInsertId();
	}
}