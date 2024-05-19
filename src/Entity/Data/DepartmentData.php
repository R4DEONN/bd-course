<?php
declare(strict_types=1);

namespace App\Entity\Data;

class DepartmentData
{
	private int $id;
	private string $city;
	private string $address;
	private int $workersCount;

	/**
	 * @param int $id
	 * @param string $city
	 * @param string $address
	 * @param int $workersCount
	 */
	public function __construct(int $id, string $city, string $address, int $workersCount)
	{
		$this->id = $id;
		$this->city = $city;
		$this->address = $address;
		$this->workersCount = $workersCount;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getAddress(): string
	{
		return $this->address;
	}

	/**
	 * @return string
	 */
	public function getCity(): string
	{
		return $this->city;
	}

	/**
	 * @return int
	 */
	public function getWorkersCount(): int
	{
		return $this->workersCount;
	}
}