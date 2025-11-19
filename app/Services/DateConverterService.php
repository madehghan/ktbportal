<?php

namespace App\Services;

class DateConverterService
{
    /**
     * Convert Jalali date to Gregorian
     * 
     * @param string $jalaliDate Format: YYYY/MM/DD or YYYY-MM-DD
     * @return string Gregorian date in Y-m-d format
     */
    public static function jalaliToGregorian(string $jalaliDate): string
    {
        // Remove any extra characters and normalize
        $jalaliDate = str_replace(['/', '-'], '/', $jalaliDate);
        $parts = explode('/', $jalaliDate);
        
        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('Invalid Jalali date format');
        }
        
        [$jy, $jm, $jd] = array_map('intval', $parts);
        
        return self::jalaliToGregorianDate($jy, $jm, $jd);
    }
    
    /**
     * Convert Gregorian date to Jalali
     * 
     * @param string $gregorianDate Format: Y-m-d
     * @return string Jalali date in YYYY/MM/DD format
     */
    public static function gregorianToJalali(string $gregorianDate): string
    {
        $parts = explode('-', $gregorianDate);
        
        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('Invalid Gregorian date format');
        }
        
        [$gy, $gm, $gd] = array_map('intval', $parts);
        
        return self::gregorianToJalaliDate($gy, $gm, $gd);
    }
    
    /**
     * Core Jalali to Gregorian conversion
     */
    private static function jalaliToGregorianDate(int $jy, int $jm, int $jd): string
    {
        $jy += 1595;
        $days = 365 * $jy + (int)(($jy / 33)) * 8 + (int)((($jy % 33) + 3) / 4) + 78 + $jd;
        
        if ($jm < 7) {
            $days += ($jm - 1) * 31;
        } else {
            $days += ($jm - 7) * 30 + 186;
        }
        
        $gy = 400 * (int)($days / 146097);
        $days %= 146097;
        
        $flag = true;
        if ($days >= 36525) {
            $days--;
            $gy += 100 * (int)($days / 36524);
            $days %= 36524;
            if ($days >= 365) {
                $days++;
            } else {
                $flag = false;
            }
        }
        
        $gy += 4 * (int)($days / 1461);
        $days %= 1461;
        
        if ($flag) {
            if ($days >= 366) {
                $days--;
                $gy += (int)($days / 365);
                $days %= 365;
            }
        }
        
        $gm = 0;
        $sal_a = [0, 31, 28 + (($gy % 4 == 0 && $gy % 100 != 0) || $gy % 400 == 0 ? 1 : 0), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        
        for ($gm = 0; $gm < 13 && $days > $sal_a[$gm]; $gm++) {
            $days -= $sal_a[$gm];
        }
        
        return sprintf('%04d-%02d-%02d', $gy, $gm, $days);
    }
    
    /**
     * Core Gregorian to Jalali conversion
     */
    private static function gregorianToJalaliDate(int $gy, int $gm, int $gd): string
    {
        $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        
        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = 355666 + (365 * $gy) + (int)(($gy2 + 3) / 4) - (int)(($gy2 + 99) / 100) + (int)(($gy2 + 399) / 400) + $gd + $g_d_m[$gm - 1];
        
        $jy = -1595 + (33 * (int)($days / 12053));
        $days %= 12053;
        
        $jy += 4 * (int)($days / 1461);
        $days %= 1461;
        
        if ($days > 365) {
            $jy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        
        if ($days < 186) {
            $jm = 1 + (int)($days / 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + (int)(($days - 186) / 30);
            $jd = 1 + (($days - 186) % 30);
        }
        
        return sprintf('%04d/%02d/%02d', $jy, $jm, $jd);
    }
}

