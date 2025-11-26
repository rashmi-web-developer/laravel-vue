<?php

$a = [];
for ($i =0; $i < 10; $i++) {
    $a[] = random_int(1,20);
}
$filter = array_filter($a, fn($n) => $n < 10);
$filter = array_values($filter);
echo "original array:\n";
print_r($a);
echo "filtered array:\n";
print_r($filter);