<?php

namespace App\Model\Entities;

use DateTime;
use PDO;

class Comment extends Entity
{
    /**
     * id of comment
     *
     * @var int
     */
    protected int $id;

    /**
     * content of comment
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
     * date of last modification
     *
     * @var string|null
     */
    protected ?string $modifiedAt = null;

    /**
     * publish or unpublish
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
     * id of publisher
     *
     * @var int|null
     */
    protected ?int $publishUserId = null;

    /**
     * post id linked with comment
     *
     * @var int
     */
    protected int $postId;

    /**
     * User id
     *
     * @var int
     */
    protected int $userId;

    /**
     * name of the user
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
     * @return null|string
     */
    public function getModifiedAt(): ?string
    {
        return $this->modifiedAt;
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
        if (str_contains($excerpt, "<img ")) {
            $excerpt =  substr($this->content, 0, (int)strpos($this->content, "<img ")) . '...';
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
     * @return null|string
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
     *
     * @return void
     */
    public function setUsername(string $name): void
    {
        $this->username = $name;
    }

}
