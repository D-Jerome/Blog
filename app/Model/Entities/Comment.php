<?php

namespace App\Model\Entities;

use DateTime;
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
	 * @return DateTime
	 */
	public function getCreatedAt(): DateTime
	{
		return $this->createdAt;
	}
	
	
	/**
	 * getModifiedAt
	 *
	 * @return DateTime
	 */
	public function getModifiedAt(): DateTime
	{
		return $this->ModifiedAt;
	}
	
	
	/**
	 * getPostId
	 *
	 * @return int
	 */
	public function getPostId(): int
	{
		return $this->postId;
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
	 * getUsername
	 *
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->username;
	}

	
	/**
	 * getExcerptContent
	 *
	 * @return string
	 */
	public function getExcerptContent(): string
	{
		$excerpt = substr($this->content, 0, 60) . '...';
		if (str_contains($excerpt, "<img ")){
			$excerpt =  substr($this->content, 0, strpos($this->content, "<img ")) . '...';
		}
		return $excerpt;
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
	 * @return DateTime
	 */
	public function getPublishAt(): DateTime
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
