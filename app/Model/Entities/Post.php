<?php

namespace App\Model\Entities;

use PDO;

class Post extends Entity
{
	protected int $id;
	protected string $name;
	protected string $slug;
	protected string $content;
	protected  $createdAt;
	protected string $excerptContent;
	protected array $categories = [];


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

	public function getExcerptContent()
	{
		return substr($this->content, 0, 60) . '...';
	}
		
	public function getCategories(): ?array
    {
        return $this->categories;
    }

}
