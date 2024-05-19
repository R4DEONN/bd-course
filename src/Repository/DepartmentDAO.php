<?php
declare(strict_types=1);

namespace App\Repository;

use App\Common\Database\ConnectionProvider;
use App\Entity\Data\DepartmentData;
use PDO;

class DepartmentDAO
{
	private \App\Common\Database\Connection $connection;

	public function __construct()
	{
		$this->connection = ConnectionProvider::getConnection();
	}

	/**
	 * @return DepartmentData[]
	 */
	public function listAll(): array
	{
		$query = <<<SQL
			SELECT 
			    department.department_id, 
			    department.city, 
			    department.address,
			    COUNT(worker_id) as worker_count
			FROM department
				LEFT JOIN bd_course.worker w on department.department_id = w.department_id
			GROUP BY department.department_id, department.city, department.address
			ORDER BY department_id
			SQL;

		$rows = $this->connection->execute($query)->fetchAll(PDO::FETCH_ASSOC);

		$departmentsData = [];
		foreach ($rows as $department)
		{
			$departmentsData[] = new DepartmentData(
				id: $department['department_id'],
				city: $department['city'],
				address: $department['address'],
				workersCount: $department['worker_count']
			);
		}

		return $departmentsData;
	}
}