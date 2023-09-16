<?php
require_once '../src/ArrMate.php';

// Get the first element of an array
$first = ArrMate::first(array(1, 2, 3, 4, 5));
echo $first; // 1

// Get the middle element of an array
$middle = ArrMate::middle(array(1, 2, 3, 4, 5));
echo $middle; // 3

// Get the last element of an array
$last = ArrMate::last(array(1, 2, 3, 4, 5));
echo $last; // 5

// Convert multi-dimensional array to single-dimensional array
$multi = ArrMate::dot(array('foo' => array('bar' => 'baz'), 'far' => array('boo' => array('fad' => 'faz'))));
echo $multi; // array('foo.bar' => 'baz', 'far.boo.fad' => 'faz')

// Retrieve a specific value from a collection of arrays
$collection = array(
    array('name' => 'John', 'age' => 21),
    array('name' => 'Jane', 'age' => 22),
    array('name' => 'Jack', 'age' => 23),
    array('name' => 'Jill', 'age' => 24),
);
$names = ArrMate::pluck($collection, 'name');
echo $names; // array('John', 'Jane', 'Jack', 'Jill')

// Retrieve a specific value from a collection of arrays and use it as the key
$collection = array(
    array('name' => 'John', 'age' => 21),
    array('name' => 'Jane', 'age' => 22),
    array('name' => 'Jack', 'age' => 23),
    array('name' => 'Jill', 'age' => 24),
);
$names = ArrMate::pluck($collection, 'name', 'age');
echo $names; // array(21 => 'John', 22 => 'Jane', 23 => 'Jack', 24 => 'Jill')

// Use ArrMate to find the youngest person
$ageNameMap = ArrMate::pluck($people, 'name', 'age');
$youngestAge = min(array_keys($ageNameMap));
$youngestName = $ageNameMap[$youngestAge];
echo "The youngest person is $youngestName, aged $youngestAge.";