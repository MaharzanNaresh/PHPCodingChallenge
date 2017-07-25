<?php


use PHPUnit\Framework\TestCase;

class RecipeTest extends TestCase
{

    private $obj;
    private $recipeTest;

    protected function setUp()
    {
        $this->recipeTest = new Challenge\Recipe();

        $reflectionClass = new \ReflectionClass("\Challenge\Recipe");
        $privateCreateObjectInvoke = $reflectionClass->getProperty("testMode");
        $privateCreateObjectInvoke->setAccessible(true);
        $privateCreateObjectInvoke->setValue($this->recipeTest, true);

        $this->obj = $this->getMockBuilder('Challenge\FileHandler')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetFridgeList()
    {
        $expected = $this->getFridgeList();
        $mockData = [
            'data' => 'Item,Amount,Unit,Use-By
bread,10,slices,25/12/2012
cheese,10,slices,25/12/2012
butter,250,grams,25/12/2012
peanut butter,250,grams,2/12/2012
mixed salad,150,grams,26/10/2012'
        ];
        $this->obj->expects($this->any())
            ->method('getUserData')
            ->willReturn($mockData);

        $this->recipeTest->getFridgeList();

        $reflectionClass = new \ReflectionClass("\Challenge\Recipe");
        $privateCreateObjectInvoke = $reflectionClass->getProperty("fridgeList");
        $privateCreateObjectInvoke->setAccessible(true);

        $result = $privateCreateObjectInvoke->getValue($this->recipeTest);
        $this->assertEquals($expected, $result);


    }

    public function testGetRecipeList()
    {
        $jsonData = $this->getRecipeList();
        $this->recipeTest->getRecipeList();

        $reflectionClass = new \ReflectionClass("\Challenge\Recipe");
        $privateCreateObjectInvoke = $reflectionClass->getProperty("recipeList");
        $privateCreateObjectInvoke->setAccessible(true);

        $result = $privateCreateObjectInvoke->getValue($this->recipeTest);
        $this->assertEquals($jsonData, $result);
    }

    public function testSortByDate()
    {
        $data = $this->recipeTest->sortByDate($this->getFridgeList());
        $this->assertInternalType('array', $data);
    }

    public function testIsItemUseAble()
    {
        $fridgeList = $this->getFridgeList();
        $item = array_shift($fridgeList);
        $result = $this->recipeTest->isItemUseAble($item);
        $this->assertFalse($result);
    }

    public function testGetRecipeItems()
    {
        $expected = array(
            'grilled cheese on toast' => array("bread", "cheese"),
            'salad sandwich' => array("bread", "mixed salad")
        );
        $reflectionClass = new \ReflectionClass("\Challenge\Recipe");
        $privateCreateObjectInvoke = $reflectionClass->getProperty("recipeList");
        $privateCreateObjectInvoke->setAccessible(true);

        $privateCreateObjectInvoke->setValue($this->recipeTest, $this->getRecipeList());
        $result = $this->recipeTest->getRecipeItems();

        $this->assertEquals($expected, $result);
    }

    public function testPrepareRecipe()
    {
        $recipeList = $this->getRecipeList();
        $fridgeList = $this->getFridgeList();

        $reflectionClass = new \ReflectionClass("\Challenge\Recipe");

        $recipeListObj = $reflectionClass->getProperty("recipeList");
        $recipeListObj->setAccessible(true);
        $recipeListObj->setValue($this->recipeTest, $recipeList);

        $recipeListObj = $reflectionClass->getProperty("fridgeList");
        $recipeListObj->setAccessible(true);
        $recipeListObj->setValue($this->recipeTest, $fridgeList);

        $result = $this->recipeTest->prepareRecipe();
        $this->assertEquals('Order Takeout', $result);
    }

    private function getFridgeList()
    {
        return [
            [
                'Item' => "bread",
                'Amount' => "10",
                'Unit' => "slices",
                'Use-By' => "25/12/2012"
            ],

            [
                'Item' => "cheese",
                'Amount' => "10",
                'Unit' => "slices",
                'Use-By' => "25/12/2012"
            ],
            [
                'Item' => "butter",
                'Amount' => "250",
                'Unit' => "grams",
                'Use-By' => "25/12/2012"
            ],
            [
                'Item' => "peanut butter",
                'Amount' => "250",
                'Unit' => "grams",
                'Use-By' => "2/12/2012"
            ],
            [
                'Item' => "mixed salad",
                'Amount' => "150",
                'Unit' => "grams",
                'Use-By' => "26/10/2012"
            ]
        ];
    }

    private function getRecipeList()
    {
        $json = '[
            {
              "name": "grilled cheese on toast",
              "ingredients": [
                {
                  "item": "bread",
                  "amount": "2",
                  "unit": "slices"
                },
                {
                  "item": "cheese",
                  "amount": "2",
                  "unit": "slices"
                }
              ]
            },
            {
              "name": "salad sandwich",
              "ingredients": [
                {
                  "item": "bread",
                  "amount": "2",
                  "unit": "slices"
                },
                {
                  "item": "mixed salad",
                  "amount": "100",
                  "unit": "grams"
                }
              ]
            }
        ]';
        return json_decode($json);
    }
}