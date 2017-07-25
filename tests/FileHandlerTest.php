<?php

use PHPUnit\Framework\TestCase;

class FileHandlerTest extends TestCase
{
    protected $fileHandler;

    protected function setUp()
    {
        $this->fileHandler = new Challenge\FileHandler();

    }

    protected function tearDown()
    {

    }

    public function testCheckFileExists()
    {
        $this->expectException(\RuntimeException::class);
        $this->fileHandler->checkFileExists(__DIR__.'/res/fridge.csvs');
    }

    public function testGetFileName()
    {
        $filename = $this->fileHandler->getFileName(__DIR__.'/res/fridge.csv');
        $this->assertEquals('fridge.csv', $filename);
    }

    public function testGetFileExtension()
    {
        $extension = $this->fileHandler->getFileExtension('fridge.csv');
        $this->assertEquals('csv', $extension);
    }

    public function testValidateFileExtensionWithCorrectValue()
    {
        $res = $this->fileHandler->validateFileExtension('fridge.csv', 'csv');
        $this->assertTrue($res);
    }


    public function testValidateFileExtensionWithIncorrectValue()
    {
        $this->expectException(\RuntimeException::class);
        $this->fileHandler->validateFileExtension('fridge.csv', 'tsv');
    }
}