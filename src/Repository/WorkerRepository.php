<?php
declare(strict_types=1);

namespace App\Repository;

use App\Common\Database\ConnectionProvider;
use App\Common\Database\DatabaseDateFormat;
use App\Entity\Worker;
use PDO;

class WorkerRepository
{
	private \App\Common\Database\Connection $connection;

	public function __construct()
	{
		$this->connection = ConnectionProvider::getConnection();
	}

	/**
	 * @throws \Exception
	 */
	public function findOneById(int $id): ?Worker
	{
		$query = <<<SQL
			SELECT
			    worker_id,
			    full_name,
			    job_title,
			    phone, 
			    email,
			    gender,
			    birth_date,
			    hire_date,
			    description,
			    avatar_path,
			    department_id
			FROM worker
			WHERE worker_id = :worker_id
			SQL;

		$statement = $this->connection->execute($query, [
			'worker_id' => $id,
		]);

		$row = $statement->fetch(PDO::FETCH_ASSOC);
		return $row ? new Worker(
			id: $row['worker_id'],
			fullName: $row['full_name'],
			jobTitle: $row['job_title'],
			phone: $row['phone'],
			email: $row['email'],
			isMale: (bool)$row['gender'],
			birthDate: new \DateTimeImmutable($row['birth_date']),
			hireDate: new \DateTimeImmutable($row['hire_date']),
			description: $row['description'],
			avatarPath: $row['avatar_path'],
			departmentId: $row['department_id']
		) : null;
	}

	public function save(Worker $worker): int
	{
		$workerId = $worker->getId();
		if ($workerId)
		{
			$this->updateWorker($worker);
		}
		else
		{
			$workerId = $this->insertWorker($worker);
			$worker->assignIdentifier($workerId);
		}

		return $workerId;
	}

	private function insertWorker(Worker $worker): int
	{
		$query = <<<SQL
			INSERT INTO worker (full_name,
			                    job_title,
			                    phone,
			                    email,
			                    gender,
			                    birth_date,
			                    hire_date,
			                    description,
			                    avatar_path,
			                    department_id
			)
			VALUES (
			        :full_name,
			        :job_title,
			        :phone,
			        :email,
			        :gender,
			        :birth_date,
			        :hire_date,
			        :description,
			        :avatar_path,
			        :department_id
			)
			SQL;
		$this->connection->execute($query, [
			'full_name' => $worker->getFullName(),
			'job_title' => $worker->getJobTitle(),
			'phone' => $worker->getPhone(),
			'email' => $worker->getEmail(),
			'gender' => $worker->isMale(),
			'birth_date' => $worker->getBirthDate()->format(DatabaseDateFormat::MYSQL_DATETIME_FORMAT),
			'hire_date' => $worker->getHireDate()->format(DatabaseDateFormat::MYSQL_DATETIME_FORMAT),
			'description' => $worker->getDescription(),
			'avatar_path' => $worker->getAvatarPath(),
			'department_id' => $worker->getDepartmentId()
		]);

		return $this->connection->getLastInsertId();
	}

	public function deleteById(int $id): void
	{
		$query = <<<SQL
			DELETE 
			FROM worker
			WHERE worker_id = :worker_id
			SQL;

		$this->connection->execute($query, [
			'worker_id' => $id,
		]);
	}

	/**
	 * @param int $id
	 * @return ?Worker[]
	 * @throws \Exception
	 */
	public function findWorkersByDepartmentId(int $id): ?array
	{
		$query = <<<SQL
			SELECT
			    worker_id,
			    full_name,
			    job_title, 
			    phone, 
			    email, 
			    gender, 
			    birth_date, 
			    hire_date,
			    description,
			    avatar_path,
			    department_id
			FROM worker
			WHERE department_id = :department_id
			SQL;

		$statement = $this->connection->execute($query, [
			'department_id' => $id,
		]);

		$workersData = $statement->fetchAll(PDO::FETCH_ASSOC);
		$workers = [];
		foreach ($workersData as $workerData)
		{
			$workers[] = $this->hydrateWorker($workerData);
		}

		return $workers;
	}

	private function hydrateWorker(array $workerData): Worker
	{
		return new Worker(
			id: $workerData['worker_id'],
			fullName: $workerData['full_name'],
			jobTitle: $workerData['job_title'],
			phone: $workerData['phone'],
			email: $workerData['email'],
			isMale: (bool)$workerData['gender'],
			birthDate: new \DateTimeImmutable($workerData['birth_date']),
			hireDate: new \DateTimeImmutable($workerData['hire_date']),
			description: $workerData['description'],
			avatarPath: $workerData['avatar_path'],
			departmentId: $workerData['department_id'],
		);
	}

	public function getDepartmentIdByWorkerId(int $workerId): int
	{
		$query = <<<SQL
			SELECT 
			    department_id
			FROM worker
			WHERE worker_id = :worker_id
			SQL;

		$statement = $this->connection->execute($query, [
			'worker_id' => $workerId,
		]);

		return $statement->fetch(PDO::FETCH_COLUMN);
	}

	private function updateWorker(Worker $worker): void
	{
		$query = <<<SQL
            UPDATE worker
            SET
              	worker_id = :worker_id,
              	full_name = :full_name,
              	job_title = :job_title,
              	phone = :phone,
              	email = :email,
              	gender = :gender,
            	birth_date = :birth_date,
            	hire_date = :hire_date,
            	description = :description,
            	avatar_path = :avatar_path,
            	department_id = :department_id
            WHERE worker_id = :worker_id
            SQL;
		$params = [
			'worker_id' => $worker->getId(),
			'full_name' => $worker->getFullName(),
			'job_title' => $worker->getJobTitle(),
			'phone' => $worker->getPhone(),
			'email' => $worker->getEmail(),
			'gender' => $worker->isMale(),
			'birth_date' => $worker->getBirthDate()->format(DatabaseDateFormat::MYSQL_DATETIME_FORMAT),
			'hire_date' => $worker->getHireDate()->format(DatabaseDateFormat::MYSQL_DATETIME_FORMAT),
			'description' => $worker->getDescription(),
			'avatar_path' => $worker->getAvatarPath(),
			'department_id' => $worker->getDepartmentId()
		];

		$stmt = $this->connection->execute($query, $params);
		if (!$stmt->rowCount())
		{
			throw new \RuntimeException("Optimistic lock failed for worker {$worker->getId()}");
		}
	}
}