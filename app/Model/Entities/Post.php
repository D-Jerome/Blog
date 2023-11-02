<?php

namespace App\Model\Entities;

use PDO;

class Post extends Entity
{
	protected int $id;
	protected string $name;
	protected string $slug;
	protected string $content;
	protected $createdAt;
	protected $modifiedAt;
	protected bool $publishState;
    protected $publishAt; 
	protected int $publishUserId;
	protected string $excerptContent;
	protected array $categories = [];
	protected int $countComments;
	protected int $userId;
	protected string $username;

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getSlug()
	{
		return $this->slug;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	public function getModifiedAt()
	{
		return $this->ModifiedAt;
	}

	public function getUserId(): int
	{
		return $this->userId;
	}

	public function getExcerptContent()
	{
		return substr($this->content, 0, 60) . '...';
	}

	public function getCategories(): ?array
	{
		return $this->categories;
	}

	public function getCountComments(): int
	{
		return $this->countComments;
	}

	public function getUsername(): string
	{
		return $this->username;
	}

	public function getPublishState()
	{
		return $this->publishState;
	}
	
	public function getPublishAt()
	{
		return $this->publishAt;
	}

	public function getPublishUserId()
	{
		return $this->publishUserId;
	}
}
