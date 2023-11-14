<?php

namespace Framework\Helpers;

use App\Model\Manager\CategoryManager;
use Framework\Application;

class FilterBuilder
{
    private array $sort;

    private array $dir;

    private array $categories;

    private array $categoriesNames;


    /**
     * __construct : Construct filter data
     *
     * @param  array $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->sort = $config['sort'];
        $this->dir = $config['dir'];
        if ($config['categories']) {
            $this->categories = $config['categories'];
            $categories = new CategoryManager(Application::getDatasource());
            $this->categoriesNames = $categories->getAll();
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
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }


    /**
     * getCategoriesNames: names of each category
     *
     * @return array
     */
    public function getCategoriesNames(): array
    {
        return $this->categoriesNames;
    }
}
