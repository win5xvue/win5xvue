<?php

namespace Tests\Unit;

use Tests\TestCase;
use ReeceM\Mocker\Mocked;
use ReeceM\Mocker\Utils\VarStore;

class MockedTest extends TestCase
{
    /**
     * Test if values are set in a mocked class
     * @test
     * @return void
     */
    public function mocked_returns_set_value_test()
    {
        // give us a clean store;
        VarStore::destroy();
        $mocked = new Mocked('user', VarStore::singleton());
        // what would be set in a class when using Mocked::class
        $mocked->name->class = 'test';

        $this->assertStringContainsString('=> "test"', (string)$mocked->name->class);
        $this->assertSame('user->name->class => "test"', (string)$mocked->name->class);
        $this->assertNotSame('user->name->class => "test"', (string)$mocked->name->class->data);
    }
    /**
     * Test if the un-set mocked returns the calls chained
     * @test
     * @return void
     */
    public function mocked_returns_chained_only_test()
    {
        // give us a clean store;
        VarStore::destroy();
        $mocked = new Mocked('user', VarStore::singleton());

        $this->assertSame('user', (string)$mocked);
        $this->assertSame('user->name', (string)$mocked->name);
        $this->assertSame('user->name->class->data', (string)$mocked->name->class->data);
    }

    /**
     * test to see if we can manipulate arrays as objects as well as an array
     * @test
     * @return void
     */
    public function mocked_handles_arrays_test()
    {
        // give us a clean store;
        VarStore::destroy();
        $mocked = new Mocked('arrays', VarStore::singleton());

		$mocked['variable']['on']['an_array'] = ['new' => 'array'];

		$this->assertIsArray($mocked['variable']['on']['an_array']);
        $this->assertArrayHasKey('variable', $mocked->__getStore()['arrays']);
        $this->assertArrayHasKey('on', $mocked['variable']);
		$this->assertArrayHasKey('an_array', $mocked['variable']['on']);

        $this->assertArrayHasKey('key_was_not_entered', $mocked['key_was_not_entered']);
	}

    /**
     * The values are not copied between areas
	 *
     * @test
     * @return void
     */
    public function mocked_does_not_cross_pollute_values_test()
    {
        // give us a clean store;
        VarStore::destroy();
        $mocked = new Mocked('content_test', VarStore::singleton());

		$mocked['variable']['on']['an_']['array'] = 'to be or not to be';
        $mocked->value->does->not->duplicate = 'maybe';
        $this->assertNotSame($mocked->value->does->not->duplicate, $mocked['variable']['on']['an_']['array']);
    }

    /**
     * test if the arrayobject will work in foreach statements
     * @test
     * @depends mocked_handles_arrays_test
     * @return void
     */
    public function array_object_loops_test()
    {
        // give us a clean store;
        VarStore::destroy();
        $mocked = new Mocked('arrays', VarStore::singleton());
        $mocked['variable'] = 'a little string of tests is a list';
        $mocked['another']  = 'a little string of tests is a list';

        foreach ($mocked as $key => $value) {
            $this->assertTrue(array_key_exists($key, $mocked->__getStore()), 'Key was not set in the mocked array');
        }

    }
}
