<?php

require __DIR__ . '/../vendor/autoload.php';

$recipe = new Challenge\Recipe();

echo $recipe->getRecipe();