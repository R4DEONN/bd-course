<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Common\AbstractDatabaseTestCase;
use Symfony\Component\BrowserKit\Response;

class WorkerControllerTest extends AbstractDatabaseTestCase
{
	public function testCreateEditAndDeleteWorker(): void
	{
		$departmentId = $this->doCreateDepartment(
			city: 'Йошкар-Ола',
			address: 'Вознесенская, 110'
		);

		$workerId = $this->doCreateWorker(
			fullName: 'Кирилл Борисов',
			jobTitle: 'Уборщик',
			phone: '+79379337070',
			email: 'mew@mew.com',
			isMale: '1',
			birthDate: '2017-02-01 00:00:00',
			hireDate: '2018-02-01 00:00:00',
			description: 'Он слишком хорош',
			departmentId: $departmentId
		);

		$response = $this->doGetWorkerPage($workerId);
		$responseContent = $response->getContent();
		$expectedString = 'Кирилл Борисов';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = 'Уборщик';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = '+79379337070';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = 'mew@mew.com';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = 'Мужской';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = '02.01.2017';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = '02.01.2018';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = 'Он слишком хорош';
		$this->assertStringContainsString($expectedString, $responseContent);

		$this->doEditWorker(
			workerId: $workerId,
			fullName: 'Кирилл Борисов1',
			jobTitle: 'Уборщик1',
			phone: '+793793370701',
			email: 'mew@mew.com1',
			isMale: '0',
			birthDate: '2017-02-02 00:00:00',
			hireDate: '2018-02-02 00:00:00',
			description: 'Он слишком хорош1',
			departmentId: $departmentId
		);
		$response = $this->doGetWorkerPage($workerId);
		$responseContent = $response->getContent();
		$expectedString = 'Кирилл Борисов1';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = 'Уборщик1';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = '+793793370701';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = 'mew@mew.com1';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = 'Женский';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = '02.02.2017';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = '02.02.2018';
		$this->assertStringContainsString($expectedString, $responseContent);
		$expectedString = 'Он слишком хорош1';
		$this->assertStringContainsString($expectedString, $responseContent);

		$this->doDeleteWorker($workerId);
		$this->doGetWorkerPage($workerId, true);
	}

	private function doCreateWorker(
		string $fullName,
		string $jobTitle,
		string $phone,
		string $email,
		string $isMale,
		string $birthDate,
		string $hireDate,
		string $description,
		int $departmentId
	): int
	{
		$response = $this->sendPostRequest(
			"/worker/add/{$departmentId}",
			[
				'fullName' => $fullName,
				'jobTitle' => $jobTitle,
				'phone' => $phone,
				'email' => $email,
				'isMale' => $isMale,
				'birthDate' => $birthDate,
				'hireDate' => $hireDate,
				'description' => $description,
			]
		);
		// Проверяем HTTP Status Code ответа
		$this->assertStatusCode(302, $response);

		$this->assertResponseRedirects();
		$redirectUrl = $response->getHeaders()['location'][0];

		$this->assertMatchesRegularExpression('/^\/worker\/\d+$/', $redirectUrl, 'Redirect URL does not match the expected pattern.');

		preg_match('/^\/worker\/(\d+)$/', $redirectUrl, $matches);
		$workerId = $matches[1];
		$this->assertIsNumeric($departmentId, 'Department ID should be numeric');

		return (int)$workerId;
	}

	private function doGetWorkerPage(int $workerId, bool $isExcept = false): Response
	{
		$response = $this->sendGetRequest(
			"/worker/{$workerId}",
			[]
		);

		if ($isExcept)
		{
			$this->assertStatusCode(500, $response);
		}
		else
		{
			$this->assertStatusCode(200, $response);
		}

		return $response;
	}

	private function doEditWorker(
		int $workerId,
		string $fullName,
		string $jobTitle,
		string $phone,
		string $email,
		string $isMale,
		string $birthDate,
		string $hireDate,
		string $description,
		int $departmentId,
	): void
	{
		$response = $this->sendPostRequest(
			"/department/{$departmentId}/worker/edit/{$workerId}",
			[
				'fullName' => $fullName,
				'jobTitle' => $jobTitle,
				'phone' => $phone,
				'email' => $email,
				'isMale' => $isMale,
				'birthDate' => $birthDate,
				'hireDate' => $hireDate,
				'description' => $description,
			]
		);

		// Проверяем HTTP Status Code ответа
		$this->assertStatusCode(302, $response);
	}

	private function doDeleteWorker(int $workerId): void
	{
		$response = $this->sendDeleteRequest(
			"/worker/delete/{$workerId}",
			[]
		);

		// Проверяем HTTP Status Code ответа
		$this->assertStatusCode(200, $response);
	}
}