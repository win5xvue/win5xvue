<?php

namespace Tests;

use ReeceM\Mocker\Mocked;

class ClassOfTest
{
    public $user;
    public $aData;
    public $bData;

    /**
     * This is a construct method
     * @param \Mocked $user mocked because its easier to mass $mock->all()
     * @param mixed $data
     */
    public function __construct(Mocked $user, $data)
    {
        $data->complex->var->that->is->set->too = "Hello World";

        $this->user = $user;
        $this->aData = $data->firstValue;
        $this->bData = $data->complex->var->that->is->set->too;
    }

    /**
     * Return a string to trigger __toString()
     */
    public function __invoke() : string
    {
        return (string)$this->bData;
    }
}