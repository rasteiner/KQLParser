<?php 

require 'Interpreter.php';

use Rasteiner\KQLParser\Interpreter;

class Cat {
    function say($what = 'meow')
    {
        return $what;
    }
    function age() {
        return 12;
    }
}

// here I give the evaluator a state to work on. These are the allowed starting variables. 
$interpreter = new Interpreter([ 
    'number1' => 1000,

    'site' => [
        'foobar' => [
            'cat' => new Cat()
        ],
        'sum' => function($a = 1, $b = 1) {
            return $a + $b;
        },
        'sumAll' => function(array $numbers) {
            return array_reduce($numbers, function($all, $one) {return $all + $one;}, 0);
        }
    ]
]);


//accessing and running stuff
var_dump($interpreter->parse('site.sum(number1, site.sum(12, site.foobar.cat.age))'));
//> float(1024)

//strings 
var_dump($interpreter->parse('site.foobar.cat.say("hello \"world\"")'));
//> string(13) "hello "world""

//strings with escape chars
var_dump($interpreter->parse('site.foobar.cat.say("hello\nworld")'));
//> string(13) "hello 
//> world"

//strings with escaped escape chars
var_dump($interpreter->parse('site.foobar.cat.say("hello\\\\nworld")'));
//> string(13) "hello\nworld"

//strings with ignored escape chars
var_dump($interpreter->parse('site.foobar.cat.say(\'hello\nworld\')'));
//> string(13) "hello\nworld"


// arrays
var_dump($interpreter->parse('site.sumAll([1, 2, 3])'));
//> float(6)

// complex arrays
var_dump($interpreter->parse('site.sumAll([number1, site.sum(1000, site.foobar.cat.age), 12])'));
//> float(2024)


// implicit method calling on objects
var_dump($interpreter->parse('site.foobar.cat.say'));
//> string(4) "meow"