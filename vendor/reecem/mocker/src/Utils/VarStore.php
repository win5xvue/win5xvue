<?php

namespace ReeceM\Mocker\Utils;

/**
 * Store implementing singleton style
 */
class VarStore
{

    /**
     * The memorised list of args
     * @var array $memoized
     */
	protected $memoized = array();

	/**
	 * The base Mocker value
	 *
	 * @var \ReeceM\Mocker\Mocked $mocked
	 */
	protected $mocked;

    /**
     * The instance of the singleton
     *
     * @var self $instance
     */
    private static $instance;

    // The constructor is private
    // to prevent initiation with outer code.
    private function __construct()
    {
        // no expensive calls unless ??
    }

    /**
     * Creates the singleton for the VarStore method
     * The object is created from within the class itself
     * only if the class has no instance.
     * @return self
     */
    public static function singleton()
    {
        if (self::$instance == null) {
            self::$instance = new VarStore();
        }

        return self::$instance;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __destruct()
    {
        self::$instance = null;
    }

    public static function destroy()
    {
        self::$instance = null;
    }
}
