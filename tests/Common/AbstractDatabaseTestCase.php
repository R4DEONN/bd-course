<?php
declare(strict_types=1);

namespace App\Tests\Common;

use App\Common\Database\Connection;
use App\Common\Database\ConnectionProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Response;

//TODO AbstractDatabaseTestCase в AbstractComponentTestCase
abstract class AbstractDatabaseTestCase extends WebTestCase
{
	private Connection $connection;
	private AbstractBrowser $client;

	// Вызывается перед каждым тестирующим методом
	//TODO: Продублировать код в AbstractFunctionalTestCase
	protected function setUp(): void
	{
		parent::setUp();

		$this->client = static::createClient();

		// Всегда начинаем транзакцию, чтобы не применять изменений к базе данных.
		$this->connection = ConnectionProvider::getConnection();
		$this->connection->beginTransaction();
	}

	// Вызывается после каждого тестирующего метода
	protected function tearDown(): void
	{
		// Всегда откатываем транзакцию, чтобы не применять изменений к базе данных.
		$this->connection->rollback();
		parent::tearDown();
		$this->restoreExceptionHandler();
	}

	private function restoreExceptionHandler(): void
	{
		while (true) {
			$previousHandler = set_exception_handler(static fn() => null);

			restore_exception_handler();

			if ($previousHandler === null) {
				break;
			}

			restore_exception_handler();
		}
	}

	protected function doCreateDepartment(string $city, string $address): int
	{
		$response = $this->sendPostRequest(
			'/department/add',
			[
				'city' => $city,
				'address' => $address,
			]
		);
		// Проверяем HTTP Status Code ответа
		$this->assertStatusCode(302, $response);

		$this->assertResponseRedirects();
		$redirectUrl = $response->getHeaders()['location'][0];

		//TODO: вспомогательный метод с паттерном, который бы возвращал строкой первое совпадение
		$this->assertMatchesRegularExpression('/^\/department\/\d+$/', $redirectUrl, 'Redirect URL does not match the expected pattern.');
		preg_match('/^\/department\/(\d+)$/', $redirectUrl, $matches);

		$departmentId = $matches[1];
		$this->assertIsNumeric($departmentId, 'Department ID should be numeric');

		return (int)$departmentId;
	}


	protected function assertStatusCode(int $statusCode, Response $response): void
	{
		$this->assertEquals($statusCode, $response->getStatusCode(), "status code must be $statusCode");
	}

	final protected function getConnection(): Connection
	{
		return $this->connection;
	}

	/**
	 * Отправляет POST запрос, передавая параметры в теле запроса в формате "application/x-www-form-urlencoded"
	 *
	 * @param string $urlPath
	 * @param array $requestParams
	 * @return Response
	 */
	protected function sendPostRequest(string $urlPath, array $requestParams): Response
	{
		return $this->doRequest('POST', $urlPath, $requestParams);
	}

	/**
	 * Отправляет DELETE запрос, передавая параметры в теле запроса
	 *
	 * @param string $urlPath
	 * @param array $requestParams
	 * @return Response
	 */
	protected function sendDeleteRequest(string $urlPath, array $requestParams): Response
	{
		return $this->doRequest('DELETE', $urlPath, $requestParams);
	}

	/**
	 * Отправляет GET запрос, передавая параметры через URL Query.
	 *
	 * @param string $urlPath
	 * @param array $queryParams
	 * @return Response
	 */
	protected function sendGetRequest(string $urlPath, array $queryParams): Response
	{
		$urlString = $urlPath . '?' . http_build_query($queryParams);
		return $this->doRequest('GET', $urlString);
	}

	private function doRequest(string $method, string $url, array $body = []): Response
	{
		$this->client->request($method, $url, $body);
		return $this->client->getInternalResponse();
	}
}