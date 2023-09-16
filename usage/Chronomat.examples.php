<?php
require_once '../src/Chronomat.php';

// Get the current date and time
$currentDateTime = Chronomat::now();
echo "Current Date and Time: $currentDateTime\n";

// Parse a date string into a DateTime object
$dateString = "2023-09-16 14:30:00";
$parsedDate = Chronomat::parse($dateString);
echo "Parsed Date: " . Chronomat::format($parsedDate) . "\n";

// Calculate the difference between two dates
$startDate = Chronomat::parse("2023-09-10");
$endDate = Chronomat::parse("2023-09-16");
$interval = Chronomat::diff($startDate, $endDate);
echo "Days between start and end date: " . $interval->d . " days\n";

// Calculate moon phase
$moonPhase = Chronomat::moon_phase_name($parsedDate);
$moonIcon = Chronomat::moon_phase_icon($parsedDate);
echo "Moon Phase: $moonPhase $moonIcon\n";

// Calculate age based on birthdate
$birthdate = "1990-05-15";
$age = Chronomat::age_calculation($birthdate);
echo "Age: $age years\n";

// Calculate relative time
$someDate = Chronomat::parse("2023-09-15 12:00:00");
$relativeTime = Chronomat::relative_time($someDate);
echo "Relative Time: $relativeTime\n";