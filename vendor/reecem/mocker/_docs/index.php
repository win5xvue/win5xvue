<?php
/**
 * https://stackoverflow.com/questions/11121613/casting-object-to-array-any-magic-method-being-called
 * https://www.php.net/manual/en/class.arrayaccess.php
 * https://php.net/manual/en/class.arrayobject.php
 * LOL - not the only one to do this, mine has memory though :) https://www.php.net/manual/en/language.oop5.iterations.php#81508
 */
// class Test extends ArrayObject {
//     private $container = array();
//     private $lastOffset;

//     public function __construct($history = 0, $input = array() , int $flags = 0, string $iterator_class = "ArrayIterator")
//     {
//         $this->lastOffset = $history;
//         parent::__construct($input, $flags, $iterator_class);
//     }

//     public function offsetSet($offset, $value) {
//         if (is_null($offset)) {
//             $this->container[] = $value;
//         } else {
//             $this->lastOffset = $offset;
//             $this->container[$offset] = $value;
//             return $this->offsetGet($offset);
//         }
//     }

//     public function offsetGet($offset) {
//         $this->lastOffset = $offset;
//         return isset($this->container[$offset]) ? $this->container[$offset] : $this->offsetSet($offset, new Test($this->lastOffset));
//     }

//     public function __toString()
//     {
//         return 'from array '. (string)$this->lastOffset;
//     }

//     public function __toArray()
//     {
//         return json_encode($this);
//     }

//     public function __get($value)
//     {
//         return 'from object ' . $value;
//     }
// }

class MockedArray extends ArrayObject{
    private $container = array();
    private $lastOffset;

    public function __construct($history = 0, $oldContainer = [])
    {
        $this->lastOffset = $history;
        $this->container = $oldContainer;
        parent::__construct();
    }

    public function getIterator() {
        return new ArrayIterator($this->container);
    }


    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->lastOffset = $offset;
            $this->container[$offset] = $value;
            return $this->offsetGet($offset);
        }
    }

    public function offsetGet($offset) {
        $this->lastOffset = $offset;
        return isset($this->container[$offset]) ? $this->container[$offset] : $this->offsetSet($offset, new MockedArray($offset, $this->container));
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function __toString()
    {
        printf('container: %s </br> offset: %s </br> </br>', json_encode($this->container), $this->lastOffset) .PHP_EOL;
        if(isset($this->container[$this->lastOffset])) {
            return (json_encode($this->container[$this->lastOffset]));
        }
        return 'from array '. (string)$this->lastOffset;
    }

    // public function __toArray()
    // {
    //     return json_encode($this);
    // }

    public function __get($value)
    {
        return 'from object ' . $value;
    }

    // public function __call($name, $arguments)
    // {
    //     $methods = (new \ReflectionClass(array_reverse(debug_backtrace(false))[0]['class']))->getMethods();
    //     var_export($methods);
    //     echo PHP_EOL;
    //     die(1);
    // }

    public static function __callStatic($name, $arguments)
    {
        
    }

    private function arrayTrace()
    {
        // one up...
        $selfTrace = debug_backtrace(false, 2);
        return [
            'function'  => $selfTrace[1]['function'] == 'offsetGet' ? '__get' : '__set',
            'args'      => $selfTrace[1]['args'],
            'type'      => $selfTrace[1]['type']
        ];
        // return new Mocked(debug_backtrace(false, 1), $this->store, $this->trace);
    }
}


$obj = new MockedArray();    

$obj['hello']['chips'] = 2;
echo isset($obj['hello']['chips']) . '</br>';

foreach ($obj['list'] as $key => $value) {
    echo 'echo </br>' ;
    echo $value . PHP_EOL;
}

$obj['test'] = ['key' => 'value'];
$obj['tesffo'] = 'hello';
echo json_encode($obj) . PHP_EOL;
echo $obj['tesffo'] . PHP_EOL;
var_export($obj->__toArray());

echo $obj['address']['street']->OBJECT . PHP_EOL;
