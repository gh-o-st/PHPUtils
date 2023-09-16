<?php

/**
 * Chronomat - A PHP utility class for date and time operations.
 *
 * This class provides a collection of static methods to perform various date and time operations
 * such as calculating date differences, working with moon phases, and managing date ranges.
 */
class Chronomat {
    /**
     * Get the current date and time.
     *
     * @param string $format (Optional) The format to return the date and time in.
     * @return string|int    The current date and time.
     */
    public static function now(?string $format = null) {
        $currentDateTime = new DateTime();
    
        if ($format !== null) {
            return $currentDateTime->format($format);
        }
    
        return $currentDateTime->getTimestamp();
    }    

    /**
     * Parse a date string into a DateTime object.
     *
     * @param string $date   The date string to parse.
     * @param string $format (Optional) The format of the input date string.
     * @return DateTime      A DateTime object representing the parsed date.
     */
    public static function parse(string $date, ?string $format = null) {
        if ($format === null) {
            return new DateTime($date);
        }
    
        return DateTime::createFromFormat($format, $date);
    }    

    /**
     * Format a DateTime object as a string.
     *
     * @param DateTime $datetime The DateTime object to format.
     * @param string   $format   (Optional) The format to use for formatting.
     * @return string            The formatted date and time string.
     */
    public static function format(DateTime $datetime, ?string $format = null) {
        if ($format === null) {
            return $datetime->format(DateTime::ATOM);
        }
    
        return $datetime->format($format);
    }    

    /**
     * Calculate the difference between two dates or DateInterval objects.
     *
     * @param mixed $date1      The first date (DateTime object or date string).
     * @param mixed $date2      The second date (DateTime object or date string).
     * @param bool  $absolute   (Optional) Whether to return an absolute DateInterval.
     * @return DateInterval     The difference between the two dates.
     */
    public static function diff($date1, $date2, bool $absolute = false) {
        if (!$date1 instanceof DateTime) {
            $date1 = new DateTime($date1);
        }
    
        if (!$date2 instanceof DateTime) {
            $date2 = new DateTime($date2);
        }
    
        $interval = $date1->diff($date2);
    
        if ($absolute) {
            $interval->invert = 0;
        }
    
        return $interval;
    }    

    /**
     * Add or subtract a DateInterval from a DateTime object.
     *
     * @param DateTime     $datetime   The DateTime object to modify.
     * @param DateInterval $interval   The DateInterval to add or subtract.
     * @param bool         $subtract   (Optional) Whether to subtract the interval.
     * @return DateTime                The modified DateTime object.
     */
    public static function modify(DateTime $datetime, DateInterval $interval, bool $subtract = false) {
        $modifiedDatetime = clone $datetime;
    
        if ($subtract) {
            $modifiedDatetime->sub($interval);
        } else {
            $modifiedDatetime->add($interval);
        }
    
        return $modifiedDatetime;
    }
    
    /**
     * Get the number of days in a month.
     *
     * @param int $month    The month to get the number of days for.
     * @param int $year     (Optional) The year to get the number of days for.
     * @return int          The number of days in the month.
     */
    public static function days_in_month(int $month, ?int $year = null) {
        if ($year === null) {
            $year = date('Y');
        }
    
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    /**
     * Calculate the moon phase for a given date.
     *
     * This method calculates the moon phase for a specific date and returns
     * an integer representing the phase, ranging from 0 to 29, where 0
     * corresponds to the New Moon and 14 corresponds to the Full Moon.
     *
     * @param DateTime $datetime The date for which to calculate the moon phase.
     *
     * @return int The moon phase as an integer from 0 to 29.
     */
    public static function moon_phase_calc(DateTime $datetime) {
        $year = $datetime->format('Y');
        $month = $datetime->format('n');
        $day = $datetime->format('j');
    
        $c = (int) ($month < 3);
        $e = $year + $c;
        $jd = floor(365.25 * $e) + floor(30.6001 * ($c + $month)) + $day + 1720994.5;
    
        $ip = fmod(0.40828 * sin(0.104258 * ($jd - 0.631578) + 1.914 * sin(0.017202 * ($jd - 0.631578))), 1);
        $ag = 0.5 - $ip;
        $ip = $ip + $ag + 0.5;
    
        return floor($ip * 29.53);
    }

    /**
     * Get the moon phase name for a given date.
     *
     * This method calculates the moon phase for a specific date and returns
     * a string representing the phase, ranging from 'New Moon' to 'Full Moon'.
     *
     * @param DateTime $datetime The date for which to calculate the moon phase.
     *
     * @return string The moon phase as a string.
     */
    public static function moon_phase_name(DateTime $datetime) {
        $phase = self::moon_phase_calc($datetime);
    
        return match($phase) {
            0 => 'New Moon',
            1, 2, 3, 4, 5, 6 => 'Waxing Crescent',
            7 => 'First Quarter',
            8, 9, 10, 11, 12, 13, 14 => 'Waxing Gibbous',
            15 => 'Full Moon',
            16, 17, 18, 19, 20, 21 => 'Waning Gibbous',
            22 => 'Last Quarter',
            23, 24, 25, 26, 27, 28 => 'Waning Crescent',
            default => 'New Moon',
        };
        
    }

    /**
     * Get the moon phase icon for a given date.
     *
     * This method calculates the moon phase for a specific date and returns
     * a string representing the phase, ranging from '🌑' to '🌕'.
     *
     * @param DateTime $datetime The date for which to calculate the moon phase.
     *
     * @return string The moon phase as a string.
     */
    public static function moon_phase_icon(DateTime $datetime) {
        $phase = self::moon_phase_calc($datetime);
        
        return match($phase) {
            0, 29 => '🌑',
            1, 2, 3, 4, 5, 6 => '🌒',
            7, 8, 9, 10, 11, 12 => '🌓',
            13, 14 => '🌔',
            15, 16, 17, 18, 19, 20 => '🌕',
            21, 22, 23, 24, 25, 26 => '🌖',
            27, 28 => '🌘',
            default => '🌑',
        };
        
    }

    /**
     * Calculate the age based on a given birthdate.
     *
     * This method calculates a person's age based on their birthdate and the
     * current date, returning the age as an integer.
     *
     * @param string $birthdate A string representing the birthdate in "YYYY-MM-DD" format.
     *
     * @return int The age calculated based on the provided birthdate.
     */
    public static function age_calculation(string $birthdate) {
        $birthDate = new DateTime($birthdate);
        $currentDate = new DateTime();
        $ageInterval = $birthDate->diff($currentDate);
        $age = $ageInterval->y;
    
        return $age;
    }
    
    /**
     * Calculate and return a human-readable relative time string.
     *
     * This method calculates the relative time difference between the provided
     * datetime and the current time, returning a human-readable string that
     * represents the time elapsed.
     *
     * Examples of possible return values:
     * - "2 millennia ago"
     * - "1 century ago"
     * - "5 decades ago"
     * - "3 years ago"
     * - "1 month ago"
     * - "5 days ago"
     * - "2 hours ago"
     * - "1 minute ago"
     * - "just now"
     *
     * @param DateTime|string $datetime The datetime to calculate relative time from,
     *                                  either as a DateTime object or a datetime string.
     *
     * @return string The human-readable relative time string.
     */
    public static function relative_time($datetime) {
        $now = new DateTime();
        $interval = self::diff($datetime, $now);
    
        if ($interval->y >= 1000) {
            $millennia = floor($interval->y / 1000);
            return ($millennia > 1) ? $millennia . ' millennia ago' : '1 millennium ago';
        } elseif ($interval->y >= 100) {
            $centuries = floor($interval->y / 100);
            return ($centuries > 1) ? $centuries . ' centuries ago' : '1 century ago';
        } elseif ($interval->y >= 10) {
            $decades = floor($interval->y / 10);
            return ($decades > 1) ? $decades . ' decades ago' : '1 decade ago';
        } elseif ($interval->y > 0) {
            return ($interval->y > 1) ? $interval->y . ' years ago' : '1 year ago';
        } elseif ($interval->m > 0) {
            return ($interval->m > 1) ? $interval->m . ' months ago' : '1 month ago';
        } elseif ($interval->d > 0) {
            return ($interval->d > 1) ? $interval->d . ' days ago' : '1 day ago';
        } elseif ($interval->h > 0) {
            return ($interval->h > 1) ? $interval->h . ' hours ago' : '1 hour ago';
        } elseif ($interval->i > 0) {
            return ($interval->i > 1) ? $interval->i . ' minutes ago' : '1 minute ago';
        } else {
            return ($interval->s > 1) ? $interval->s . ' seconds ago' : 'just now';
        }
    }
    
    /**
     * Check if two date ranges intersect.
     *
     * @param array $range1 The first date range [start1, end1].
     * @param array $range2 The second date range [start2, end2].
     * @return bool True if the ranges intersect, false otherwise.
     */
    public static function ranges_intersect($range1, $range2) {
        return ($range1[1] >= $range2[0] && $range2[1] >= $range1[0]);
    }

    /**
     * Find the intersection between two date ranges.
     *
     * This method takes two date ranges as input and calculates their intersection,
     * if it exists. If the date ranges do not intersect, it returns null.
     *
     * @param array $range1 The first date range [start1, end1].
     * @param array $range2 The second date range [start2, end2].
     *
     * @return array|null An array representing the intersection [start, end], or null if there is no intersection.
     */
    public static function find_intersection($range1, $range2) {
        if (self::ranges_intersect($range1, $range2)) {
            return [max($range1[0], $range2[0]), min($range1[1], $range2[1])];
        }
        return null;
    }
    
    /**
     * Merge overlapping date ranges.
     *
     * This method takes an array of date ranges as input and merges any ranges
     * that overlap, returning an array of merged date ranges.
     *
     * @param array $ranges An array of date ranges.
     *
     * @return array An array of merged date ranges.
     */
    public static function merge_ranges($ranges) {
        $mergedRanges = [];
        $rangesCount = count($ranges);
    
        for ($i = 0; $i < $rangesCount; $i++) {
            $range = $ranges[$i];
            $rangeMerged = false;
    
            for ($j = 0; $j < count($mergedRanges); $j++) {
                $mergedRange = $mergedRanges[$j];
                $intersection = self::find_intersection($range, $mergedRange);
    
                if ($intersection !== null) {
                    $mergedRanges[$j] = $intersection;
                    $rangeMerged = true;
                    break;
                }
            }
    
            if (!$rangeMerged) {
                $mergedRanges[] = $range;
            }
        }
    
        return $mergedRanges;
    }
    
    /**
     * Expand a date range by adding a specified number of days to the end date.
     *
     * This method takes a date range and extends it by adding a specified number of days
     * to the end date. It returns a new date range with the extended period.
     *
     * @param array $range An array representing the original date range ['start' => DateTime, 'end' => DateTime].
     * @param int $days    The number of days to add to the end date.
     *
     * @return array An array representing the expanded date range ['start' => DateTime, 'end' => DateTime].
     */
    public static function expand_range($range, $days) {
        $newRange = [
            'start' => clone $range['start'],
            'end' => clone $range['end']
        ];
        $newRange['end']->add(new DateInterval('P' . $days . 'D'));
        return $newRange;
    }
    
    /**
     * Shrink a date range by subtracting a specified number of days from the end date.
     *
     * This method takes a date range and shrinks it by subtracting a specified number of days
     * from the end date. It returns a new date range with the shortened period.
     *
     * @param array $range An array representing the original date range ['start' => DateTime, 'end' => DateTime].
     * @param int $days    The number of days to subtract from the end date.
     *
     * @return array An array representing the shrunk date range ['start' => DateTime, 'end' => DateTime].
     */
    public static function shrink_range($range, $days) {
        $newRange = [
            'start' => clone $range['start'],
            'end' => clone $range['end']
        ];
        $newRange['end']->sub(new DateInterval('P' . $days . 'D'));
        return $newRange;
    }

    /**
     * Split a date range into sub-ranges of a specified length.
     *
     * This method takes a date range and splits it into sub-ranges of a specified length,
     * returning an array of the sub-ranges.
     *
     * @param array $range An array representing the original date range ['start' => DateTime, 'end' => DateTime].
     * @param int $days    The length of each sub-range in days.
     *
     * @return array An array of sub-ranges, each represented by an array ['start' => DateTime, 'end' => DateTime].
     */
    public static function split_range($range) {
        $startDate = $range['start'];
        $endDate = $range['end'];
        $date = $startDate;
        $subRanges = [];
        while ($date <= $endDate) {
            $nextDate = clone $date;
            $nextDate->add(new DateInterval('P1D'));
            $subRanges[] = ['start' => clone $date, 'end' => $nextDate];
            $date = $nextDate;
        }
        return $subRanges;
    }
    
    /**
     * Check if there is any overlap between a date range and a collection of other date ranges.
     *
     * This method checks if there is any overlap between a given date range and a collection of
     * existing date ranges. It returns true if an overlap is detected, otherwise false.
     *
     * @param array $dateRanges   An array of date ranges to compare against.
     * @param array $rangeToCheck The date range to check for overlap ['start' => DateTime, 'end' => DateTime].
     *
     * @return bool True if there is an overlap, false otherwise.
     */
    public static function has_overlap($dateRanges, $rangeToCheck) {
        foreach ($dateRanges as $existingRange) {
            if ($rangeToCheck['start'] <= $existingRange['end'] && $rangeToCheck['end'] >= $existingRange['start']) {
                return true; // Overlap detected
            }
        }
        return false; // No overlap found
    }

    /**
     * Find gaps between a collection of date ranges.
     *
     * This method analyzes a collection of date ranges and identifies gaps (periods of non-overlap)
     * between them. It returns an array of gap periods as date ranges.
     *
     * @param array $dateRanges An array of date ranges to analyze.
     *
     * @return array An array of gap periods represented as date ranges ['start' => DateTime, 'end' => DateTime].
     */
    public static function find_gaps($dateRanges) {
        $gaps = [];
        $dateRanges = array_values($dateRanges); // Ensure a consistent order
        for ($i = 0; $i < count($dateRanges) - 1; $i++) {
            $gapStart = clone $dateRanges[$i]['end'];
            $gapEnd = clone $dateRanges[$i + 1]['start'];
            if ($gapStart < $gapEnd) {
                $gaps[] = ['start' => $gapStart, 'end' => $gapEnd];
            }
        }
        return $gaps;
    }
    
    /**
     * Shift a date range by a specified number of days.
     *
     * This method takes a date range and shifts it by adding or subtracting a specified number of days.
     * It returns a new date range reflecting the shifted period.
     *
     * @param array $range An array representing the original date range ['start' => DateTime, 'end' => DateTime].
     * @param int $days    The number of days to add (positive) or subtract (negative) from the date range.
     *
     * @return array An array representing the shifted date range ['start' => DateTime, 'end' => DateTime].
     */
    public static function shift_range($range, $days) {
        $newRange = [
            'start' => clone $range['start'],
            'end' => clone $range['end']
        ];
        $newRange['start']->add(new DateInterval('P' . $days . 'D'));
        $newRange['end']->add(new DateInterval('P' . $days . 'D'));
        return $newRange;
    }

}
