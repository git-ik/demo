<?php

namespace core\Factory;

////////////////////////////
// FACTORY METHOD EXAMPLE //
////////////////////////////

interface PhoneFactoryInterface
{
    public function create($data = []);
}

interface Phone
{
    public function getTitle(): string;
    public function getDescription(): string;
    public function getFunctions(): array;
}

class PhoneFactory
{
    public function create($data = []): Phone
    {
        return new SimplePhone($data);
    }
}

class SmartPhoneFactory
{
    public function create($data = []): Phone
    {
        return new SmartPhone($data);
    }
}

class SimplePhone implements Phone
{
    public $title = 'Телефон';
    public $description = 'Описание телефона';
    public $functions = [
        'Звонки',
        'СМС',
        'Интернет'
    ];

    public function __construct($data = [])
    {
        foreach (get_class_vars(__CLASS__) as $fieldTitle => $fieldValue) {
            if (isset($data[$fieldTitle])) {
                $this->$fieldTitle = $data[$fieldTitle];
            }
        }
    }

    public function getTitle(): string
    {
        if (empty($this->title)) {
            return '';
        } else {
            return $this->title;
        }
    }

    public function getDescription(): string
    {
        if (empty($this->description)) {
            return '';
        } else {
            return $this->description;
        }
    }

    public function getFunctions(): array
    {
        if (empty($this->functions)) {
            return [];
        } else {
            return $this->functions;
        }
    }
}

class SmartPhone implements Phone
{
    public $title = 'Смартфон';
    public $description = 'Описание смартфона';
    public $functions = [
        'Звонки',
        'СМС',
        'Интернет',
        'GPS',
        'Установка приложений'
    ];

    public function __construct($data = [])
    {
        foreach (get_class_vars(__CLASS__) as $fieldTitle => $fieldValue) {
            if (isset($data[$fieldTitle])) {
                $this->$fieldTitle = $data[$fieldTitle];
            }
        }
    }

    public function getTitle(): string
    {
        if (empty($this->title)) {
            return '';
        } else {
            return $this->title;
        }
    }

    public function getDescription(): string
    {
        if (empty($this->description)) {
            return '';
        } else {
            return $this->description;
        }
    }

    public function getFunctions(): array
    {
        if (empty($this->functions)) {
            return [];
        } else {
            return $this->functions;
        }
    }
}

//Test phone identity
class Test
{
    public static function testPhone($phone)
    {
        if ($phone instanceof Phone) {
            return true;
        }
        return false;
    }
}
