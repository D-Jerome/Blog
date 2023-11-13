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
	
	protected $createdAt;
	
	protected $modifiedAt;
	
	protected bool $publishState;
    
	protected mixed $publishAt; 
	
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
