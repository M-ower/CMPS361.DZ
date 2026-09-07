<?php
// Part 4: Set a grade from 0 to 100.
$grade = 88;

if ($grade >= 90) {
    echo "Your grade is A.";
} elseif ($grade >= 80) {
    echo "Your grade is B.";
} elseif ($grade >= 70) {
    echo "Your grade is C.";
} else {
    echo "Your grade is Fail.";
}
?>