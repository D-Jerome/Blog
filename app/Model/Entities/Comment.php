<?php

namespace App\Model\Entities;

use PDO;

class Comment extends Entity
{
	protected int $id;
	protected string $content;
	protected $createdAt;
	protected int $postId;
	protected int $userId;


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

	public function getPostId()
	{
		return $this->postId;
	}
		
	public function userId()
    {
        return $this->userId;
    }

}
