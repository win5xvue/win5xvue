<?php

namespace Tests\Unit;

use Tests\TestCase;
use ReeceM\Mocker\ReflectionMockery;

class ReflectionMockeryTest extends TestCase
{
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function can_make_reflection_test()
    {
        $mock = new ReflectionMockery(new \ReflectionClass('Tests\ClassOfTest'));

        $class = new \ReflectionClass('Tests\ClassOfTest');

        $instance = $class->newInstanceArgs($mock->all());
        // run the invoke function to use the instantiated data
        // the result is a collection instance
        $this->assertSame('data->complex->var->that->is->set->too => "Hello World"', $instance());
    }
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function reflection_accepts_string_test()
    {
        $mock = new ReflectionMockery('Tests\ClassOfTest');

        $class = new \ReflectionClass('Tests\ClassOfTest');

        $instance = $class->newInstanceArgs($mock->all());
        // run the invoke function to use the instantiated data
        $this->assertSame('data->complex->var->that->is->set->too => "Hello World"', $instance());
    }
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function reflection_gives_magic_get_test()
    {
        $mock = new ReflectionMockery('Tests\ClassOfTest');

        $this->assertInstanceOf(\ReeceM\Mocker\Mocked::class, $mock->data);
        $this->assertInstanceOf(\ReeceM\Mocker\Mocked::class, $mock->get('data'));
        $this->assertInstanceOf(\ReeceM\Mocker\Mocked::class, $mock->user);
        $this->assertInstanceOf(\ReeceM\Mocker\Mocked::class, $mock->get('user'));
    }

    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function reflection_fails_on_wrong_data_test()
    {
        try {
            $mocked = new ReflectionMockery(new \Exception('Nope'));
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
        try {
            $mocked = new ReflectionMockery('');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
}
