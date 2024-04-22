<?php

namespace App\Controller;

use App\Entity\Department;
use App\Repository\DepartmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../../.env');

class DepartmentController extends AbstractController
{
	private DepartmentRepository $departmentRepository;

	public function __construct(DepartmentRepository $departmentRepository)
	{
		$this->departmentRepository = $departmentRepository;
	}

	public function getDepartmentPage(int $departmentId): Response
	{
		$department = $this->departmentRepository->findById($departmentId);
		return $this->render('department/departmentCard.html.twig', [
			'department' => $department,
		]);
	}

	public function getAddDepartmentPage(): Response
	{
		return $this->render('department/departmentAdd.html.twig', []);
	}

	public function addDepartment(Request $request): Response
	{
		$newDepartment = new Department(
			null,
			$request->get('city'),
			$request->get('address'),
			[]
		);
		$id = $this->departmentRepository->add($newDepartment);
		return $this->redirectToRoute('department_page', [
			'departmentId' => $id,
		]);
	}

	public function index(): Response
	{
		$departments = $this->departmentRepository->listAll();
		return $this->render('department/departmentList.html.twig', [
			'departments' => $departments,
		]);
	}
}