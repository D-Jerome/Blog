<?php

namespace App\Model\Entities;

use DateTime;
use PDO;

class Post extends Entity
{

	protected int $id;

	protected string $name;

	protected string $slug;

	protected string $content;

	protected string $createdAt;

	protected ?string $modifiedAt;

	protected bool $publishState;

	protected ?string $publishAt;

	protected ?int $publishUserId;

	protected string $excerptContent;

	protected array $categories = [];

	protected int $countComments;

	protected int $userId;

	protected string $username;


	/**
	 * getId
	 *
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}


	/**
	 * getName
	 *
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}


	/**
	 * getSlug
	 *
	 * @return string
	 */
	public function getSlug(): string
	{
		return $this->slug;
	}


	/**
	 * getContent
	 *
	 * @return string
	 */
	public function getContent(): string
	{
		return $this->content;
	}


	/**
	 * getCreatedAt
	 *
	 * @return string
	 */
	public function getCreatedAt(): string
	{
		return $this->createdAt;
	}


	/**
	 * getModifiedAt
	 *
	 * @return string
	 */
	public function getModifiedAt(): string
	{
		return $this->ModifiedAt;
	}


	/**
	 * getUserId
	 *
	 * @return int
	 */
	public function getUserId(): int
	{
		return $this->userId;
	}


	/**
	 * getExcerptContent
	 *
	 * @return void
	 */
	public function getExcerptContent()
	{
		$excerpt = substr($this->content, 0, 60) . '...';
		if (str_contains($excerpt, "<img ")){
			$excerpt =  substr($this->content, 0, strpos($this->content, "<img ")) . '...';
		}
		return $excerpt;
	}


	/**
	 * getCategories
	 *
	 * @return array
	 */
	public function getCategories(): ?array
	{
		return $this->categories;
	}


	/**
	 * getCountComments
	 *
	 * @return int
	 */
	public function getCountComments(): int
	{
		return $this->countComments;
	}


	/**
	 * getUsername
	 *
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->username;
	}


	/**
	 * getPublishState
	 *
	 * @return bool
	 */
	public function getPublishState(): bool
	{
		return $this->publishState;
	}


	/**
	 * getPublishAt
	 *
	 * @return string
	 */
	public function getPublishAt(): string
	{
		return $this->publishAt;
	}


	/**
	 * getPublishUserId
	 *
	 * @return int
	 */
	public function getPublishUserId(): int
	{
		return $this->publishUserId;
	}


}
