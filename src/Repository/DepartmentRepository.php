<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Department;
use App\Entity\Worker;
use Exception;
use PDO;

readonly class DepartmentRepository
{
	private Connection $connection;

	public function __construct()
	{
		$this->connection = new Connection(
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

		$departmentsData = $this->connection->execute($query, [])->fetchAll(PDO::FETCH_ASSOC);
		$departments = [];
		foreach ($departmentsData as $departmentData) {
			$workers = $this->findWorkersByDepartmentId($departmentData['department_id']);

			$departments[] = new Department(
				$departmentData['department_id'],
				$departmentData['city'],
				$departmentData['address'],
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
		$statement = $this->connection->execute($query, [
			'department_id' => $id,
		]);

		$workersData = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($workersData as $workerData) {
			$workers[] = new Worker(
				$workerData['worker_id'],
				$workerData['full_name'],
				$workerData['job_title'],
				$workerData['phone'],
				$workerData['email'],
				(bool)$workerData['gender'],
				new \DateTimeImmutable($workerData['birth_date']),
				new \DateTimeImmutable($workerData['hire_date']),
				$workerData['description'],
			);
		}

		return $workers;
	}

	/**
	 * @throws Exception
	 */
	public function findById(int $id): ?Department
	{
		$query = <<<SQL
			SELECT *
			FROM department
			WHERE department_id = :department_id
			SQL;

		$statement = $this->connection->execute($query, [
			'department_id' => $id,
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
		$statement = $this->connection->execute($query, [
			':city' => $department->getCity(),
			':address' => $department->getAddress(),
		]);

		return $this->connection->getLastInsertId();
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
}