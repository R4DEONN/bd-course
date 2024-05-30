<?php
declare(strict_types=1);

namespace App\Entity\Data;

class DepartmentData
{
	public function __construct(
		private int    $id,
		private string $city,
		private string $address,
		private int    $workersCount,
	)
	{
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