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
    protected ?string $modifiedAt;

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
    protected ?string $publishAt;

    /**
     * publisher id
     *
     * @var int|null
     */
    protected ?int $publishUserId;

    /**
     * excerpt of the content
     *
     * @var string
     */
    protected string $excerptContent;

    /**
     * list of categories
     *
     * @var array<int, string>
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
        return $this->modifiedAt;
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
     * @return string
     */
    public function getExcerptContent(): string
    {
        $excerpt = substr($this->content, 0, 60) . '...';
        if (str_contains($excerpt, "<img ")) {
            $excerpt =  substr($this->content, 0, strpos($this->content, "<img ")) . '...';
        }
        return $excerpt;
    }


    /**
     * getCategories
     *
     * @return null|array<int, string>
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
