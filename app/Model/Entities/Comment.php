<?php

declare(strict_types=1);

namespace App\Model\Entities;

class Comment extends Entity
{
    /**
     * id of comment
     */
    protected int $id;

    /**
     * content of comment
     */
    protected string $content;

    /**
     * date of creation
     */
    protected string $createdAt;

    /**
     * date of last modification
     */
    protected ?string $modifiedAt = null;

    /**
     * publish or unpublish
     */
    protected bool $publishState;

    /**
     * date of publication
     */
    protected ?string $publishAt = null;

    /**
     * id of publisher
     */
    protected ?int $publishUserId = null;

    /**
     * post id linked with comment
     */
    protected int $postId;

    /**
     * User id
     */
    protected int $userId;

    /**
     * name of the user
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
     * getContent
     */
    public function getContent(): string
    {
        return $this->content;
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
     * getPostId
     */
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * getUserId
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * getUsername
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * getExcerptContent
     */
    public function getExcerptContent(): string
    {
        $excerpt = substr($this->content, 0, 60) . '...';
        if (str_contains($excerpt, '<img ')) {
            $excerpt = substr($this->content, 0, (int) strpos($this->content, '<img ')) . '...';
        }

        return $excerpt;
    }

    /**
     * getPublishState
     */
    public function getPublishState(): bool
    {
        return $this->publishState;
    }

    /**
     * getPublishAt
     */
    public function getPublishAt(): ?string
    {
        return $this->publishAt;
    }

    /**
     * getPublishUserId
     *
     * @return ?int
     */
    public function getPublishUserId(): ?int
    {
        return $this->publishUserId;
    }

    /**
     * set  the username
     *
     * @param string $name Name to set
     */
    public function setUsername(string $name): void
    {
        $this->username = $name;
    }
}
