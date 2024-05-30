<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Department;
use App\Repository\DepartmentDAO;
use App\Repository\DepartmentRepository;
use App\Repository\WorkerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->load(__DIR__ . '/../../.env');

class DepartmentController extends AbstractController
{
	private DepartmentDAO $departmentDAO;
	private DepartmentRepository $departmentRepository;
	private WorkerRepository $workerRepository;

	public function __construct(
		DepartmentDAO $departmentDAO,
		DepartmentRepository $departmentRepository,
		WorkerRepository $workerRepository
	) {
		$this->departmentDAO = $departmentDAO;
		$this->departmentRepository = $departmentRepository;
		$this->workerRepository = $workerRepository;
	}

	/**
	 * @throws \Exception
	 */
	public function showDepartmentPage(int $departmentId): Response
	{
		$department = $this->departmentRepository->findById($departmentId);
		if (!$department)
		{
			throw new \InvalidArgumentException("The department with id = " . $departmentId . " does not exist");
		}
		$workers = $this->workerRepository->findWorkersByDepartmentId($departmentId);
		return $this->render('department/departmentCard.html.twig', [
			'department' => $department,
			'workers' => $workers
		]);
	}

	/**
	 * @throws \Exception
	 */
	public function showEditDepartmentPage(int $departmentId): Response
	{
		$department = $this->departmentRepository->findById($departmentId);
		return $this->render('department/departmentAdd.html.twig', [
			'department' => $department
		]);
	}

	public function editDepartment(Request $request, int $departmentId): Response
	{
		$department = new Department(
			id: $departmentId,
			city: $request->get('city'),
			address: $request->get('address'),
		);
		$this->departmentRepository->save($department);
		return $this->redirectToRoute('departments_page', []);
	}

	public function showAddDepartmentPage(): Response
	{
		return $this->render('department/departmentAdd.html.twig', []);
	}

	public function addDepartment(Request $request): Response
	{
		$newDepartment = new Department(
			null,
			$request->get('city'),
			$request->get('address'),
		);
		$id = $this->departmentRepository->save($newDepartment);
		return $this->redirectToRoute('department_page', [
			'departmentId' => $id,
		]);
	}

	/**
	 * @throws \Exception
	 */
	public function deleteDepartment(int $departmentId): Response
	{
		$this->departmentRepository->deleteById($departmentId);
		$departmentsData = $this->departmentDAO->listAll();
		return $this->render('department/departments.html.twig', [
			'departments' => $departmentsData,
		]);
	}

	/**
	 * @throws \Exception
	 */
	public function index(): Response
	{
		$departmentsData = $this->departmentDAO->listAll();
		return $this->render('department/departmentList.html.twig', [
			'departments' => $departmentsData,
		]);
	}
}