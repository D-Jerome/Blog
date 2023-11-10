<?php

namespace App\Model\Entities;

use PDO;

class Comment extends Entity
{
	protected int $id;
	protected string $content;
	protected $createdAt;
	protected $modifiedAt;
	protected bool $publishState;
    protected $publishAt; 
	protected ?int $publishUserId;
	protected int $postId;
	protected int $userId;
	protected string $username;

	public function getId()
	{
		return $this->id;
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

	public function getPostId()
	{
		return $this->postId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getUsername()
	{
		return $this->username;
	}
	public function getExcerptContent()
	{
		$excerpt = substr($this->content, 0, 60) . '...';
		if (str_contains($excerpt, "<img ")){
			$excerpt =  substr($this->content, 0, strpos($this->content, "<img ")) . '...';
		}
		return $excerpt;
		
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
