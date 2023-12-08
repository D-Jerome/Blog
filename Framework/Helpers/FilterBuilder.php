<?php

namespace Framework\Helpers;

use App\Model\Manager\CategoryManager;
use Framework\Application;
use Framework\Config;
use Webmozart\Assert\Assert;

class FilterBuilder extends Config
{
    /**
     * List of Sortable Item
     *
     * @var array<string,string> $sort
     */
    private array $sort;

    /**
     * direction of sort
     *
     * @var array<string,string> $dir
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
     * @param string    $typeObj : Name of the object to list
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
            'listSelect'
        ];

        foreach ($filterDatas as $filterData) {
            Assert::notFalse(parent::getSpecificData($category, $typeObj, $filterData), 'Config not containing passed Data');
            if (!is_null(parent::getSpecificData($category, $typeObj, $filterData)) === true) {
                if (is_array(parent::getSpecificData($category, $typeObj, $filterData)) === true) {
                    $this->$filterData =  parent::getSpecificData($category, $typeObj, $filterData);
                }
            }
            if (!is_null($this->list)) {
                $objectManagerName = 'App\\Model\\Manager\\' . array_key_first($this->list) . 'Manager';
                $getInstance = 'get' . array_key_first($this->list) . 'Instance';
                $listNames = $objectManagerName::$getInstance(parent::getDatasource());
                $this->listNames = $listNames->getAllToList($this->list[array_key_first($this->list)]);
            }
        }
    }
    //end __construct()


    /**
     * getSort: Type and FR translate
     *
     * @return array<string,string>
     */
    public function getSort(): array
    {
        return $this->sort;
    }


    /**
     * getDir: Type and FR translate
     *
     * @return array<string,string>
     */
    public function getDir(): array
    {
        return $this->dir;
    }


    /**
     * getCategories : Type and FR translate
     *
     * @return array<string,string>|null
     */
    public function getList(): ?array
    {
        return $this->list;
    }


    /**
     * getListSelect
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
     * @return array<int,string>|null
     */
    public function getListNames(): ?array
    {
        return $this->listNames;
    }
}
