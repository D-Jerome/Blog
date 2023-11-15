<?php

namespace Framework\Helpers;

use App\Model\Manager\CategoryManager;
use Framework\Application;

class FilterBuilder
{
    private array $sort;

    private array $dir;

    private ?array $list = null ;

    private ?array $listNames = null ;


    /**
     * __construct : Construct filter data
     *
     * @param string $typeObj : Name of the object to list
     * @param  array $config
     * @return void
     */
    public function __construct(array $config, string $typeObj)
    {
        $this->sort = $config[$typeObj]['sort'];
        $this->dir = $config['dir'];
        if (!empty($config[$typeObj]['list'])) {
            $this->list = $config[$typeObj]['list'];
            $objectManagerName = 'App\\Model\\Manager\\' . array_key_first($config[$typeObj]['list']) . 'Manager';
            $listNames = new  $objectManagerName(Application::getDatasource());
            $this->listNames = $listNames->getAllToList($config[$typeObj]['list'][array_key_first($config[$typeObj]['list'])]);
        }
    }//end __construct


    /**
     * getSort: Type and FR translate
     *
     * @return array
     */
    public function getSort(): array
    {
        return $this->sort;
    }


    /**
     * getDir: Type and FR translate
     *
     * @return array
     */
    public function getDir(): array
    {
        return $this->dir;
    }


    /**
     * getCategories : Type and FR translate
     *
     * @return array|null
     */
    public function getList(): ?array
    {
        return $this->list;
    }


    /**
     * getCategoriesNames: names of each category
     *
     * @return array|null
     */
    public function getListNames(): ?array
    {
        return $this->listNames;
    }
}
