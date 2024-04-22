<?php

namespace App\Entity;

class Department
{
	private ?int $id;
	private string $city;
	private string $address;

	/**
	 * @var Worker[]
	 */
	private array $workers;

	/**
	 * @param ?int $id
	 * @param string $city
	 * @param string $address
	 * @param Worker[] $workers
	 */
	public function __construct(?int $id, string $city, string $address, array $workers)
	{
		if ($city === '')
		{
			throw new \InvalidArgumentException("City cannot be empty");
		}

		if ($address === '')
		{
			throw new \InvalidArgumentException("Address cannot be empty");
		}

		$this->id = $id;
		$this->city = $city;
		$this->address = $address;
		$this->workers = $workers;
	}

	/**
	 * @return ?int
	 */
	public function getId(): ?int
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
	 * @return Worker[]
	 */
	public function getWorkers(): array
	{
		return $this->workers;
	}

	public function getWorkersCount(): int
	{
		return count($this->workers);
	}
}