<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Common\AbstractDatabaseTestCase;
use Symfony\Component\BrowserKit\Response;

class DepartmentControllerTest extends AbstractDatabaseTestCase
{
	public function testCreateEditAndDeleteDepartment(): void
	{
		$departmentId = $this->doCreateDepartment(
			city: 'Йошкар-Ола',
			address: 'Вознесенская, 110'
		);

		$response = $this->doGetDepartmentPage($departmentId);
		$responseContent = $response->getContent();
		//TODO Проверить по селектору
		$expectedString = 'Йошкар-Ола, Вознесенская, 110';
		$this->assertStringContainsString($expectedString, $responseContent);

		$this->doEditDepartment(
			departmentId: $departmentId,
			city: 'Чебоксары',
			address: 'ул. Гагарина, 45'
		);
		$response = $this->doGetDepartmentPage($departmentId);
		$responseContent = $response->getContent();
		$expectedString = 'Чебоксары, ул. Гагарина, 45';
		$this->assertStringContainsString($expectedString, $responseContent);

		$this->doDeleteDepartment($departmentId);
		$this->doGetDepartmentPage($departmentId, true);
	}

	//TODO Передавать статус
	private function doGetDepartmentPage(int $departmentId, bool $isExcept = false): Response
	{
		$response = $this->sendGetRequest(
			"/department/{$departmentId}",
			[]
		);

		if ($isExcept)
		{
			//TODO: Возврашать либо 400, либо 404
			$this->assertStatusCode(500, $response);

		}
		else
		{
			$this->assertStatusCode(200, $response);
		}

		return $response;
	}

	private function doEditDepartment(
		int $departmentId,
		string $city,
		string $address,
	): void
	{
		$response = $this->sendPostRequest(
			"/department/edit/{$departmentId}",
			[
				'city' => $city,
				'address' => $address,
			]
		);

		// Проверяем HTTP Status Code ответа
		$this->assertStatusCode(302, $response);
	}

	private function doDeleteDepartment(int $departmentId): void
	{
		$response = $this->sendDeleteRequest(
			"/department/delete/{$departmentId}",
			[]
		);

		// Проверяем HTTP Status Code ответа
		$this->assertStatusCode(200, $response);
	}
}