<?php
declare(strict_types=1);

namespace App\Entity\Data;

class WorkerData
{
	private int $id;
	private string $fullName;
	private string $jobTitle;
	private string $phone;
	private string $email;
	private bool $isMale;
	private \DateTimeImmutable $birthDate;
	private \DateTimeImmutable $hireDate;
	private ?string $description;
	private ?string $avatarPath;
	private string $city;
	private string $address;
	private int $departmentId;

	public function __construct(
		int $id,
		string $fullName,
		string $jobTitle,
		string $phone,
		string $email,
		bool $isMale,
		\DateTimeImmutable $birthDate,
		\DateTimeImmutable $hireDate,
		?string $description,
		?string $avatarPath,
		string $city,
		string $address,
		int $departmentId
	) {
		$this->id = $id;
		$this->fullName = $fullName;
		$this->jobTitle = $jobTitle;
		$this->address = $address;
		$this->city = $city;
		$this->phone = $phone;
		$this->email = $email;
		$this->isMale = $isMale;
		$this->birthDate = $birthDate;
		$this->hireDate = $hireDate;
		$this->description = $description;
		$this->avatarPath = $avatarPath;
		$this->departmentId = $departmentId;
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
	public function getFullName(): string
	{
		return $this->fullName;
	}

	/**
	 * @return string
	 */
	public function getJobTitle(): string
	{
		return $this->jobTitle;
	}

	/**
	 * @return string|null
	 */
	public function getAvatarPath(): ?string
	{
		return $this->avatarPath;
	}

	/**
	 * @return string
	 */
	public function getPhone(): string
	{
		return $this->phone;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
	}

	/**
	 * @return bool
	 */
	public function isMale(): bool
	{
		return $this->isMale;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getBirthDate(): \DateTimeImmutable
	{
		return $this->birthDate;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getHireDate(): \DateTimeImmutable
	{
		return $this->hireDate;
	}

	/**
	 * @return string|null
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getCity(): string
	{
		return $this->city;
	}

	/**
	 * @return string
	 */
	public function getAddress(): string
	{
		return $this->address;
	}

	/**
	 * @return int
	 */
	public function getDepartmentId(): int
	{
		return $this->departmentId;
	}
}