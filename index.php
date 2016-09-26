<?php
require_once 'config.php';

// $undergrad = new undergrad;
// $undergrad->colleges();
// $undergrad->departments();
// $undergrad->programs();
// $undergrad->courses();
// $undergrad->faculty();

// $grad = new grad;
// $grad->colleges();
// $grad->departments();
// $grad->programs();
// $grad->courses();
// $grad->faculty();

$schedule = new class_schedule;
$schedule->get_schedule(2014, 4);

// 1 = Winter
// 3 = Spring
// 4 = Summer
// 5 = Fall

?>
