<?php

declare(strict_types=1);

namespace Framework\Helpers;

use Framework\Config;

class FilterBuilder extends Config
{
    /**
     * List of Sortable Item
     *
     * @var array<string,string>
     */
    private array $sort;

    /**
     * direction of sort
     *
     * @var array<string,string>
     */
    private array $dir;

    /**
     * Table and field to filter the list
     *  Object => field
     *
     * @var array<string,string>|null
     */
    private ?array $list = null;

    /**
     * Name of the list
     * param Item of object => Name display
     *
     * @var array<string,string>|null
     */
    private ?array $listSelect = null;

    /**
     * Items of the list
     *
     * @var array<int,string>|null
     */
    private ?array $listNames = null;

    /**
     * __construct : Construct filter data
     *
     * @param string $typeObj : Name of the object to list
     *
     * @return void
     */
    public function __construct(string $typeObj)
    {
        if (!isset(parent::$config)) {
            parent::__construct();
        }
        $category = 'filter';
        $filterDatas = [
            'sort',
            'dir',
            'list',
            'listSelect',
        ];

        foreach ($filterDatas as $filterData) {
            if (false !== parent::getSpecificData($category, $typeObj, $filterData)) {
                $this->$filterData = (array) parent::getSpecificData($category, $typeObj, $filterData);
            }
            if (null !== $this->list) {
                if (!empty($this->list)) {
                    $objectManagerName = 'App\\Model\\Manager\\' . array_key_first($this->list) . 'Manager';
                    $getInstance = 'get' . array_key_first($this->list) . 'Instance';
                    $listNames = $objectManagerName::$getInstance(parent::getDatasource());
                    $this->listNames = $listNames->getAllToList($this->list[array_key_first($this->list)]);
                }
            }
        }
    }
    // end __construct()

    /**
     * getSort: Type and FR translate
     *
     * @return array<string,string>
     */
    public function getSort(): array
    {
        return (array) $this->sort;
    }

    /**
     * getDir: Type and FR translate
     *
     * @return array<string,string>
     */
    public function getDir(): array
    {
        return (array) $this->dir;
    }

    /**
     * getCategories : Type and FR translate
     *
     * @return array<string,string>|null
     */
    public function getList(): ?array
    {
        return (array) $this->list;
    }

    /**
     * getListSelect
     *
     * @return array<string,string>|null
     */
    public function getListSelect(): ?array
    {
        return (array) $this->listSelect;
    }

    /**
     * getListNames: names of each option case
     *
     * @return array<int,string>|null
     */
    public function getListNames(): ?array
    {
        return $this->listNames;
    }
}
