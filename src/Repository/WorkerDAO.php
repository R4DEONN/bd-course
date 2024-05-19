<?php
declare(strict_types=1);

namespace App\Repository;

use App\Common\Database\ConnectionProvider;
use App\Entity\Data\WorkerData;
use PDO;

class WorkerDAO
{
	private \App\Common\Database\Connection $connection;

	public function __construct()
	{
		$this->connection = ConnectionProvider::getConnection();
	}

	/**
	 * @return ?WorkerData
	 * @throws \Exception
	 */
	public function findOneById(int $id): ?WorkerData
	{
		$query = <<<SQL
			SELECT 
			    d.city,
			    d.address,
			    w.worker_id,
			    w.full_name,
			    w.job_title,
			    w.phone,
			    w.email,
			    w.gender,
			    w.birth_date,
			    w.hire_date,
			    w.description,
			    w.avatar_path,
				w.department_id
			FROM worker w
				INNER JOIN bd_course.department d on w.department_id = d.department_id
			WHERE w.worker_id = :id
			SQL;

		$row = $this->connection->execute($query, [
			'id' => $id
		])->fetch(PDO::FETCH_ASSOC);

		return $row ? new WorkerData(
			id: $row['worker_id'],
			fullName: $row['full_name'],
			jobTitle: $row['job_title'],
			phone: $row['phone'],
			email: $row['email'],
			isMale: (bool) $row['gender'],
			birthDate: new \DateTimeImmutable($row['birth_date']),
			hireDate: new \DateTimeImmutable($row['hire_date']),
			description: $row['description'],
			avatarPath: $row['avatar_path'],
			city: $row['city'],
			address: $row['address'],
			departmentId: $row['department_id'],
		) : null;
	}
}