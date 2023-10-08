<?php

namespace App\Model\Entities;

class Post extends PostEntity
{
	protected int $id;
	protected string $name;
	protected string $slug;
	protected string $content;
	protected  $createdAt;
	protected string $excerptContent;

	public function getId()
	{
		return $this->id;
	}

	public function getname()
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
		return substr($this->content, 0, 60);
	}
}
