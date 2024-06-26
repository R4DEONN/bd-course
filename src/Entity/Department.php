<?php
declare(strict_types=1);

namespace App\Entity;

class Department
{
	private ?int $id;
	private string $city;
	private string $address;

	/**
	 * @param ?int $id
	 * @param string $city
	 * @param string $address
	 */
	public function __construct(?int $id, string $city, string $address)
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
	}

	public function assignIdentifier(int $id): void
	{
		$this->id = $id;
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
}