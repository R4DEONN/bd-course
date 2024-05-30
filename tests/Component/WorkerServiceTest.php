<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Entity\Department;
use App\Entity\Worker;
use App\Repository\DepartmentRepository;
use App\Repository\WorkerRepository;
use App\Tests\Common\AbstractDatabaseTestCase;

class WorkerServiceTest extends AbstractDatabaseTestCase
{
	/**
	 * @throws \Exception
	 */
	public function testCreateEditAndDeleteWorker(): void
	{
		$workerRepository = new WorkerRepository();
		$departmentRepository = new DepartmentRepository();

		$departmentId = $departmentRepository->save(new Department(
			id: null,
			city: 'Йошкар-Ола',
			address: 'Вознесенская, 110',
		));

		$workerId = $workerRepository->save(new Worker(
			id: null,
			fullName: 'Кирилл Борисов',
			jobTitle: 'Уборщик',
			phone: '+79379337070',
			email: 'mew@mew.com',
			isMale: true,
			birthDate: new \DateTimeImmutable('2017-02-01 00:00:00'),
			hireDate: new \DateTimeImmutable('2017-02-01 00:00:00'),
			description: 'Он слишком хорош',
			avatarPath: 'path',
			departmentId: $departmentId
		));

		$worker = $workerRepository->findOneById($workerId);
		$this->assertEquals('Кирилл Борисов', $worker->getFullName());
		$this->assertEquals('Уборщик', $worker->getJobTitle());
		$this->assertEquals('+79379337070', $worker->getPhone());
		$this->assertEquals('mew@mew.com', $worker->getEmail());
		$this->assertEquals(true, $worker->isMale());
		$this->assertEquals(new \DateTimeImmutable('2017-02-01 00:00:00'), $worker->getBirthDate());
		$this->assertEquals(new \DateTimeImmutable('2017-02-01 00:00:00'), $worker->getHireDate());
		$this->assertEquals('Он слишком хорош', $worker->getDescription());
		$this->assertEquals('path', $worker->getAvatarPath());
		$this->assertEquals($departmentId, $worker->getDepartmentId());

		$workerRepository->save(new Worker(
			id: $workerId,
			fullName: 'Кирилл Борисов1',
			jobTitle: 'Уборщик1',
			phone: '+793793370701',
			email: 'mew@mew.com1',
			isMale: false,
			birthDate: new \DateTimeImmutable('2017-02-01 00:00:01'),
			hireDate: new \DateTimeImmutable('2017-02-01 00:00:01'),
			description: 'Он слишком хорош1',
			avatarPath: 'path1',
			departmentId: $departmentId
		));

		$worker = $workerRepository->findOneById($workerId);
		$this->assertEquals('Кирилл Борисов1', $worker->getFullName());
		$this->assertEquals('Уборщик1', $worker->getJobTitle());
		$this->assertEquals('+793793370701', $worker->getPhone());
		$this->assertEquals('mew@mew.com1', $worker->getEmail());
		$this->assertEquals(false, $worker->isMale());
		$this->assertEquals(new \DateTimeImmutable('2017-02-01 00:00:01'), $worker->getBirthDate());
		$this->assertEquals(new \DateTimeImmutable('2017-02-01 00:00:01'), $worker->getHireDate());
		$this->assertEquals('Он слишком хорош1', $worker->getDescription());
		$this->assertEquals('path1', $worker->getAvatarPath());
		$this->assertEquals($departmentId, $worker->getDepartmentId());

		$workerRepository->deleteById($workerId);

		// Шаг 3. Assert
		$worker = $workerRepository->findOneById($workerId);
		$this->assertEquals(null, $worker);
	}

}