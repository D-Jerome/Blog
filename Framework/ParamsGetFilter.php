<?php

declare(strict_types=1);

namespace Framework;

class ParamsGetFilter
{
    /**
     * Sort field
     */
    private string $sort;

    /**
     * Direction of sort
     */
    private string $dir;

    /**
     * type of list
     */
    private ?string $list;

    /**
     * id of list selected
     */
    private ?int $listSelect;

    /**
     * UserInfo information modification
     */
    private ?string $userInfo;

    /**
     * ckeck and group filter information pass by user
     */
    public function __construct()
    {
        $filterReturn = (new HttpParams())->getParamsGet();
        $this->sort = isset($filterReturn['sort']) ? (string) ($filterReturn['sort']) : 'createdAt';
        $this->dir = isset($filterReturn['dir']) ? (string) ($filterReturn['dir']) : 'DESC';
        $this->list = empty($filterReturn['list']) ? null : (string) ($filterReturn)['list'];
        $this->userInfo = isset($filterReturn['user']) ? (string) ($filterReturn['user']) : null;

        if (isset($filterReturn['listSelect'])) {
            $this->listSelect = '---' !== $filterReturn['listSelect'] ? (int) $filterReturn['listSelect'] : null;
        } else {
            $this->listSelect = null;
        }

        if (null === $this->listSelect && null !== $this->list) {
            $this->list = null;
        }

        if (null === $this->list && null !== $this->listSelect) {
            $this->listSelect = null;
        }
    }

    /**
     * getSort
     */
    public function getSort(): string
    {
        return $this->sort;
    }

    /**
     * getDir
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * getList
     */
    public function getList(): ?string
    {
        return $this->list;
    }

    /**
     * getListSelect
     */
    public function getListSelect(): ?int
    {
        return $this->listSelect;
    }

    /**
     * getUserInfo
     */
    public function getUserInfo(): ?string
    {
        return $this->userInfo;
    }
}
