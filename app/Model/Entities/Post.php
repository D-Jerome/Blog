<?php

namespace App\Model\Entities;

use DateTime;
use PDO;

class Post extends Entity
{
    /**
     * id of Post
     *
     * @var int
     */
    protected int $id;

    /**
     * title of post
     *
     * @var string
     */
    protected string $name;

    /**
     * slug of post
     *
     * @var string
     */
    protected string $slug;

    /**
     * content of post
     *
     * @var string
     */
    protected string $content;

    /**
     * date of creation
     *
     * @var string
     */
    protected string $createdAt;

    /**
     * date of modification
     *
     * @var string|null
     */
    protected ?string $modifiedAt = null;

    /**
     * publish or not publish
     *
     * @var bool
     */
    protected bool $publishState;

    /**
     * date of publication
     *
     * @var string|null
     */
    protected ?string $publishAt = null;

    /**
     * publisher id
     *
     * @var int|null
     */
    protected ?int $publishUserId = null;

    /**
     * excerpt of the content
     *
     * @var string
     */
    protected string $excerptContent;

    /**
     * list of categories
     *
     * @var array<int, Category>
     */
    protected array $categories = [];

    /**
     * number of comments by post
     *
     * @var int
     */
    protected int $countComments;

    /**
     * id of user
     *
     * @var int
     */
    protected int $userId;

    /**
     * name of user
     *
     * @var string
     */
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
     * set name
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
     * set slug
     *
     * @return void
     */
    public function setSlug(string $text): void
    {
        $this->slug = $text;
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
     * set content
     *
     * @return void
     */
    public function setContent(string $text): void
    {
        $this->content = $text;
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
     * @return string|null
     */
    public function getModifiedAt(): ?string
    {
        return $this->modifiedAt;
    }


    /**
     * set ModifiedAt
     *
     * @return void
     */
    public function setModifiedAt(string $date): void
    {
        $this->modifiedAt = $date;
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
     * @return string|false
     */
    public function getExcerptContent(): string|false
    {
        $excerpt = substr($this->content, 0, 60) . '...';
        if (str_contains($excerpt, "<img ")) {
            $excerpt =  substr($this->content, 0, (int)strpos($this->content, "<img ")) . '...';
        }
        return $excerpt;
    }


    /**
     * getCategories
     *
     * @return null|array<int, Category>
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }


    /**
     * set ModifiedAt
     *
     * @param  array<int,Category> $categories
     * @return void
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
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
     * set CountComments
     *
     * @return void
     */
    public function setCountComments(int $count): void
    {
        $this->countComments = $count;
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
     * set username
     *
     * @return void
     */
    public function setUsername(string $name): void
    {
        $this->username = $name;
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
     * set Publish State
     *
     * @return void
     */
    public function setPublishState(bool $state): void
    {
        $this->publishState = $state;
    }


    /**
     * getPublishAt
     *
     * @return null|string
     */
    public function getPublishAt(): ?string
    {
        return $this->publishAt;
    }


     /**
     * set publishAt
     *
     * @return void
     */
    public function setPublishAt(string $date): void
    {
        $this->publishAt = $date;
    }


    /**
     * getPublishUserId
     *
     * @return int|null
     */
    public function getPublishUserId(): ?int
    {
        return $this->publishUserId;
    }


}
