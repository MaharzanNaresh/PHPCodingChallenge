<?php
declare(strict_types=1);

namespace Challenge;

class Recipe
{
    const FRIDGE_MESSAGE = "Enter Fridge list file path (CSV): \n >";
    const RECIPE_MESSAGE = "Enter Recipe list file path (JSON):\n >";
    const FRIDGE_EXTENSION = 'csv';
    const RECIPE_EXTENSION = 'json';

    private $fileHandler;
    private $recipeList;
    private $fridgeList;
    private $testMode = false;

    public function __construct()
    {
        $this->fileHandler = new FileHandler();
    }

    public function getRecipe()
    {
        $this->getFridgeList();
        $this->getRecipeList();

        return $this->prepareRecipe();
    }

    public function prepareRecipe()
    {
        $sortedFridgeList = $this->sortByDate($this->fridgeList);
        $itemCount = count(array_column($sortedFridgeList, 'Item'));
        $maxCount = $itemCount * $itemCount;
        $recipeItems = $this->getRecipeItems();
        $recipeFound = false;
        while (!$recipeFound && $maxCount > 0) {
            $recipeFoundCount = 0;
            $recipe = '';
            foreach ($sortedFridgeList as $item) {
                if (!$this->isItemUseAble($item)) {
                    continue;
                }
                if (empty($recipe)) {
                    $recipe = $this->recipeSearch($item['Item'], $recipeItems, $recipe);
                    $recipeFoundCount++;
                } else {
                    $rec = $this->recipeSearch($item['Item'], $recipeItems, $recipe);
                    if (strcmp($recipe, $rec) === 0) {
                        $recipeFoundCount++;
                    }
                }
                if (!empty($recipe)) {
                    $recipeItemsCount = count($recipeItems[$recipe]);
                    if ($recipeFoundCount === $recipeItemsCount) {
                        $recipeFound = true;
                        break;
                    }
                }
            }
            $maxCount--;
        }
        return empty($recipeFound) ? 'Order Takeout' : $recipe;
    }

    public function recipeSearch($item, $recipe, $existingRecipe)
    {
        $recipeName = '';
        foreach ($recipe as $key => $value) {
            $res = array_search($item, $value);

            if (gettype($res) == 'integer') {
                if (empty($existingRecipe)) {
                    $recipeName = $key;
                    break;
                } elseif ($existingRecipe == $key) {
                    $recipeName = $key;
                    break;
                } else {
                    continue;
                }
            }
        }
        return $recipeName;
    }

    public function getRecipeItems()
    {
        $recipeList = [];
        foreach ($this->recipeList as $recipe) {
            $name = $recipe->name;
            if (is_array($recipe->ingredients)) {
                $items = [];
                foreach ($recipe->ingredients as $key => $value) {
                    array_push($items, $value->item);
                }
            }
            $recipeList[$name] = $items;
        }
        return $recipeList;
    }

    public function isItemUseAble($item): bool
    {
        $date = str_replace('/', '-', $item['Use-By']);
        $today = date_create('now');
        $datetime = date_create($date);
        return $datetime >= $today ? true : false;
    }

    public function getFridgeList()
    {
        $fridgeData = $this->fileHandler->getUserData(self::FRIDGE_MESSAGE, self::FRIDGE_EXTENSION, ($this->testMode) ? __DIR__ . '/../tests/res/fridge.csv' : '');
        $csv = array_map('str_getcsv', str_getcsv($fridgeData['data'], "\n"));

        array_walk($csv, function (&$a) use ($csv) {
            $a = array_combine($csv[0], $a);
        });

        array_shift($csv);

        $this->fridgeList = $csv;
    }

    public function getRecipeList()
    {
        $recipeData = $this->fileHandler->getUserData(self::RECIPE_MESSAGE, self::RECIPE_EXTENSION, ($this->testMode) ? __DIR__ . '/../tests/res/recipe.json' : '');
        $this->recipeList = json_decode($recipeData['data']);
    }

    public function sortByDate($items): array
    {
        $date1 = $date2 = array();
        foreach ($items as $key => $row) {
            $date2 = explode('/', $row['Use-By']);
            $newDate = $date2[1] . '/' . $date2[0] . '/' . $date2[2];
            $date1[$key] = strtotime($newDate);
        }
        array_multisort($date1, SORT_ASC, $items);
        return $items;
    }

}