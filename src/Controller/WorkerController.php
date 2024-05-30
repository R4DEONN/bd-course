<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Worker;
use App\Repository\DepartmentRepository;
use App\Repository\WorkerDAO;
use App\Repository\WorkerRepository;
use App\Service\ImageUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkerController extends AbstractController
{
	private DepartmentRepository $departmentRepository;
	private WorkerRepository $workerRepository;
	private WorkerDAO $workerDAO;
	private ImageUploader $imageService;

	public function __construct(
		DepartmentRepository $departmentRepository,
		WorkerRepository $workerRepository,
		WorkerDAO $workerDAO,
		ImageUploader $imageService
	) {
		$this->departmentRepository = $departmentRepository;
		$this->workerRepository = $workerRepository;
		$this->workerDAO = $workerDAO;
		$this->imageService = $imageService;
	}

	/**
	 * @throws \Exception
	 */
	public function showAddWorkerPage(int $departmentId): Response
	{
		$department = $this->departmentRepository->findById($departmentId);
		return $this->render('worker/workerAdd.html.twig', [
			'department' => $department,
		]);
	}

	public function addWorker(Request $request, int $departmentId): Response
	{
		$imageData = $request->files->get('avatar');
		$avatarPath = null;
		if ($imageData !== null)
		{
			$avatarPath = $this->imageService->moveImageToUploadsAndGetPath($imageData);
		}
		$newWorker = new Worker(
			id: null,
			fullName: $request->get('fullName'),
			jobTitle: $request->get('jobTitle'),
			phone: $request->get('phone'),
			email: $request->get('email'),
			isMale: (bool)$request->get('isMale'),
			birthDate: new \DateTimeImmutable($request->get('birthDate')),
			hireDate: new \DateTimeImmutable($request->get('hireDate')),
			description: $request->get('description'),
			avatarPath: $avatarPath,
			departmentId: $departmentId
		);

		$id = $this->workerRepository->save($newWorker);
		return $this->redirectToRoute('worker_page', [
			'workerId' => $id,
		]);
	}

	/**
	 * @throws \Exception
	 */
	public function showWorkerCardPage(int $workerId): Response
	{
		$worker = $this->workerDAO->findOneById($workerId);
		if (!$worker)
		{
			throw new \InvalidArgumentException('The worker id is not available');
		}
		return $this->render('worker/workerCard.html.twig', [
			'worker' => $worker,
		]);
	}

	/**
	 * @throws \Exception
	 */
	public function showWorkerEditPage(int $departmentId, int $workerId): Response
	{
		$department = $this->departmentRepository->findById($departmentId);
		$worker = $this->workerRepository->findOneById($workerId);
		return $this->render('worker/workerAdd.html.twig', [
			'department' => $department,
			'worker' => $worker,
		]);
	}

	public function editWorker(Request $request, int $departmentId, int $workerId): Response
	{
		$imageData = $request->files->get('avatar');
		$avatarPath = null;
		if ($imageData !== null)
		{
			$avatarPath = $this->imageService->moveImageToUploadsAndGetPath($imageData);
		}
		$worker = new Worker(
			id: $workerId,
			fullName: $request->get('fullName'),
			jobTitle: $request->get('jobTitle'),
			phone: $request->get('phone'),
			email: $request->get('email'),
			isMale: (bool)$request->get('isMale'),
			birthDate: new \DateTimeImmutable($request->get('birthDate')),
			hireDate: new \DateTimeImmutable($request->get('hireDate')),
			description: $request->get('description'),
			avatarPath: $avatarPath,
			departmentId: $departmentId
		);
		$this->workerRepository->save($worker);
		return $this->redirectToRoute('department_page', [
			'departmentId' => $departmentId,
		]);
	}

	/**
	 * @throws \Exception
	 */
	public function deleteWorker(int $workerId): Response
	{
		$departmentId = $this->workerRepository->getDepartmentIdByWorkerId($workerId);
		$this->workerRepository->deleteById($workerId);
		$department = $this->departmentRepository->findById($departmentId);
		$workers = $this->workerRepository->findWorkersByDepartmentId($departmentId);
		return $this->render('department/workers.html.twig', [
			'department' => $department,
			'workers' => $workers
		]);
	}
}