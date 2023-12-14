<?php

declare(strict_types=1);

namespace App\Model\Entities;

class Post extends Entity
{
    /**
     * id of Post
     */
    protected int $id;

    /**
     * title of post
     */
    protected string $name;

    /**
     * slug of post
     */
    protected string $slug;

    /**
     * content of post
     */
    protected string $content;

    /**
     * date of creation
     */
    protected string $createdAt;

    /**
     * date of modification
     */
    protected ?string $modifiedAt = null;

    /**
     * publish or not publish
     */
    protected bool $publishState;

    /**
     * date of publication
     */
    protected ?string $publishAt = null;

    /**
     * publisher id
     */
    protected ?int $publishUserId = null;

    /**
     * excerpt of the content
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
     */
    protected int $countComments;

    /**
     * id of user
     */
    protected int $userId;

    /**
     * name of user
     */
    protected string $username;

    /**
     * getId
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * getName
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * set name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * getSlug
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * set slug
     */
    public function setSlug(string $text): void
    {
        $this->slug = $text;
    }

    /**
     * getContent
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * set content
     */
    public function setContent(string $text): void
    {
        $this->content = $text;
    }

    /**
     * getCreatedAt
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * getModifiedAt
     */
    public function getModifiedAt(): ?string
    {
        return $this->modifiedAt;
    }

    /**
     * set ModifiedAt
     */
    public function setModifiedAt(string $date): void
    {
        $this->modifiedAt = $date;
    }

    /**
     * getUserId
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * getExcerptContent
     */
    public function getExcerptContent(): string | false
    {
        $excerpt = substr($this->content, 0, 60) . '...';
        if (str_contains($excerpt, '<img ')) {
            $excerpt = substr($this->content, 0, (int) strpos($this->content, '<img ')) . '...';
        }

        return $excerpt;
    }

    /**
     * getCategories
     *
     * @return array<int, Category>|null
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }

    /**
     * set ModifiedAt
     *
     * @param array<int,Category> $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * getCountComments
     */
    public function getCountComments(): int
    {
        return $this->countComments;
    }

    /**
     * set CountComments
     */
    public function setCountComments(int $count): void
    {
        $this->countComments = $count;
    }

    /**
     * getUsername
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * set username
     */
    public function setUsername(string $name): void
    {
        $this->username = $name;
    }

    /**
     * getPublishState
     */
    public function getPublishState(): bool
    {
        return $this->publishState;
    }

    /**
     * set Publish State
     */
    public function setPublishState(bool $state): void
    {
        $this->publishState = $state;
    }

    /**
     * getPublishAt
     */
    public function getPublishAt(): ?string
    {
        return $this->publishAt;
    }

    /**
     * set publishAt
     */
    public function setPublishAt(string $date): void
    {
        $this->publishAt = $date;
    }

    /**
     * getPublishUserId
     */
    public function getPublishUserId(): ?int
    {
        return $this->publishUserId;
    }
}
