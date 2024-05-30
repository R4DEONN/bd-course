<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Entity\Department;
use App\Repository\DepartmentRepository;
use App\Tests\Common\AbstractDatabaseTestCase;

class DepartmentServiceTest extends AbstractDatabaseTestCase
{
	public function testCreateEditAndDeleteDepartment(): void
	{
		$departmentRepository = new DepartmentRepository();

		$departmentId = $departmentRepository->save(new Department(
			id: null,
			city: 'Йошкар-Ола',
			address: 'Вознесенская, 110',
		));

		$department = $departmentRepository->findById($departmentId);
		//TODO: Вынести во вспомагательный метод
		$this->assertEquals('Йошкар-Ола', $department->getCity());
		$this->assertEquals('Вознесенская, 110', $department->getAddress());

		$departmentRepository->save(new Department(
			id: $departmentId,
			city: 'Чебоксары',
			address: 'Волкова, 108',
		));

		$department = $departmentRepository->findById($departmentId);
		//TODO: Вынести во вспомагательный метод
		//TODO: Добавить message во вспомагательный метод
		$this->assertEquals('Чебоксары', $department->getCity());
		$this->assertEquals('Волкова, 1088', $department->getAddress(), 'department address');

		$departmentRepository->deleteById($departmentId);

		$department = $departmentRepository->findById($departmentId);
		$this->assertNull($department);
	}
}