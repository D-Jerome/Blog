<?php

namespace Framework\Helpers;

use App\Model\Manager\CategoryManager;
use Framework\Application;

class FilterBuilder
{

    /**
     * List of Sortable Item
     *
     * @var array<string, string> $sort
     */
    private array $sort;

    /**
     * direction of sort
     *
     * @var array<string, string> $dir
     */
    private array $dir;

    /**
     * Table and field to filter the list
     *  Object => field
     *
     * @var array<string,string>|null $list
     */
    private ?array $list = null ;

    /**
     * Name of the list
     * param Item of object => Name display
     *
     * @var array<string,string>|null $listSelect
     */
    private ?array $listSelect = null;

    /**
     * Items of the list
     *
     * @var array<int,string>|null $listNames
     */
    private ?array $listNames = null;


    /**
     * __construct : Construct filter data
     *
     * @param string                                                                          $typeObj : Name of the object to list
     * @param array<string, bool|int|string|array<string, string|array<string, string>|null>> $config
     *
     * @return void
     */
    public function __construct(array $config, string $typeObj)
    {
        $this->sort = $config[$typeObj]['sort'];
        $this->dir = $config['dir'];
        if (!empty($config[$typeObj]['list'])) {
            $this->list = $config[$typeObj]['list'];
            $this->listSelect = $config[$typeObj]['listSelect'];
            $objectManagerName = 'App\\Model\\Manager\\' . array_key_first($config[$typeObj]['list']) . 'Manager';
            $listNames = new  $objectManagerName(Application::getDatasource());
            $this->listNames = $listNames->getAllToList($config[$typeObj]['list'][array_key_first($config[$typeObj]['list'])]);
        }

    }//end __construct


    /**
     * getSort: Type and FR translate
     *
     * @return array<string, string>
     */
    public function getSort(): array
    {
        return $this->sort;
    }


    /**
     * getDir: Type and FR translate
     *
     * @return array<string, string>
     */
    public function getDir(): array
    {
        return $this->dir;
    }


    /**
     * getCategories : Type and FR translate
     *
     * @return array<string, string>|null
     */
    public function getList(): ?array
    {
        return $this->list;
    }


    /**
     * [Description for getListSelect]
     *
     * @return array<string,string>|null
     */
    public function getListSelect(): ?array
    {
        return $this->listSelect;
    }

    /**
     * getListNames: names of each option case
     *
     * @return array<int, string>|null
     */
    public function getListNames(): ?array
    {
        return $this->listNames;
    }

}
