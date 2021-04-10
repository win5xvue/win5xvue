<?php

namespace ReeceM\Mocker;

use ReeceM\Mocker\Mocked;
use ReeceM\Mocker\Utils\VarStore;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class ReflectionMockery
{
    /**
     * The File from the reflection result
     * @var string $file
     */
    private $file;
    /**
     * The reflection class instance
     * @var \ReflectionClass $reflection
     */
    private $reflection;

    /**
     * all the args for the class
     */
    public $__args;

    /**
     * Construct the Reflection Mocker Class
     *
     * @param string|\ReflectionClass $reflect namespace or Reflection
     */
    public function __construct($reflect)
    {
        if ($reflect instanceof \ReflectionClass) {
            $reflection = $reflect;
        } elseif (is_string($reflect)) {
            try {
                $reflection = new \ReflectionClass($reflect);
            } catch (\Exception $th) {
                throw $th;
            }
        } else {
            throw new \Exception('$reflect not a `string` or instance of \ReflectionClass ¯\_(ツ)_/¯');
        }

        $this->file = (new Filesystem())->get($reflection->getFileName());
        $this->reflection = $reflection;

        $this->reflectionExtractWantedArgs();
    }

    public function reflectionNewClass(string $name = 'ReflectionMockery'): Mocked
    {
        return new Mocked($name, VarStore::singleton());
    }

    public function reflectionExtractWantedArgs()
    {
        $params = $this->reflection->getConstructor()->getParameters();

        foreach ($params as $param) {
            $matches = [];
            $argName = $param->name;
            $result = $this->reflectionNewClass($argName);
            // preg_match_all('/(?<matched>\$' . $argName . '->.*[^;\n])/', $this->file, $matches);
            preg_match_all('/(?<matched>\$' . $argName . '->.+?\b)/', $this->file, $matches);

            foreach ($matches['matched'] as $key) {
                $name = preg_replace('/(\$' . $argName . '->)/', '', $key);
                $result->$name = $this->reflectionNewClass($argName);
            }

            Arr::set($this->__args, $argName, $result);
        }
    }

    public function __get($value): Mocked
    {
        return Arr::get($this->__args, $value, $this->reflectionNewClass());
    }

    public function get($value): Mocked
    {
        return Arr::get($this->__args, $value, $this->reflectionNewClass());
    }

    /**
     * return an array of all the arguments found
     * @param array $exclude
     * @return array
     */
    public function all($exclude = []): array
    {
        return Arr::except($this->__args, $exclude);
    }
}
