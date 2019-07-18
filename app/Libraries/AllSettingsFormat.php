<?php

namespace App\Libraries;


use App\Models\Setting;
use League\Flysystem\Config;

/**
 * @property  getLanguage
 */
class AllSettingsFormat
{

    private $settingConfig;

    public function __construct()
    {
        $this->settingConfig = new Config();
        $settingValue = Setting::all();

        foreach ($settingValue as $item) {

            $this->settingConfig->set($item->setting_name, $item->setting_value);
        }
    }

    public function getDate($date)
    {
        if ($date == null) {

            return null;

        } else {
            $dateFormate = $this->settingConfig->get('date_format');

            return date($dateFormate, strtotime($date));
        }

    }

    public function getDateFormat()
    {
        $dateFormate = $this->settingConfig->get('date_format');

        $dateFormatArray = [
            'd/m/Y' => 'dd/mm/yyyy',
            'm/d/Y' => 'mm/dd/yyyy',
            'Y/m/d' => 'yyyy/mm/dd',
            'd-m-Y' => 'dd-mm-yyyy',
            'm-d-Y' => 'mm-dd-yyyy',
            'Y-m-d' => 'yyyy-mm-dd',
            'd.m.Y' => 'dd.mm.yyyy',
            'm.d.Y' => 'mm.dd.yyyy',
            'Y.m.d' => 'yyyy.mm.dd',
        ];

        return $dateFormatArray[$dateFormate];

    }

    public function getCurrency($currencyBill)
    {

        $format = $this->settingConfig->get('currency_format');
        $symbol = $this->settingConfig->get('currency_symbol');

        if ($format && $symbol) {

            if ($format == 'left') {

                return $symbol . $currencyBill;

            } else if ($format == 'left-space') {

                return $symbol . " " . $currencyBill;

            } else if ($format == 'right-space') {

                return $currencyBill . " " . $symbol;

            } else {

                return $currencyBill . $symbol;
            }

        } else {

            return $currencyBill;
        }
    }

    public function thousandSep($val)
    {
        $format = $this->settingConfig->get('thousand_separator');
        $sep = $this->settingConfig->get('decimal_separator');
        $numDec = $this->settingConfig->get('number_of_decimal');

        if ($format || $sep || $numDec) {

            if ($format == 'space') {

                return number_format($val, (int)$numDec, $sep, " ");

            } else {

                return number_format($val, (int)$numDec, $sep, $format);
            }

        } else {

            return $val;
        }
    }

    public function timeFormat($time)
    {
        $format = $this->settingConfig->get('time_format');

        if ($format == '12h') {

            if (is_array($time)) {
                $timeArray = [];

                foreach ($time as $t) {

                    array_push($timeArray, date("g:i A", strtotime($t)));
                }

                sort($timeArray);

                return $timeArray;

            } else {

                return date("g:i A", strtotime($time));
            }

        } else {

            return $time;
        }
    }

    public function setTimeFormat($time)
    {
        if (is_array($time)) {
            $timeArray = [];

            foreach ($time as $t) {

                array_push($timeArray, date("H:i:s", strtotime($t)));

            }

            return $timeArray;

        } else {

            return date("H:i:s", strtotime($time));
        }

    }

    public function getTimeFormat()
    {
        $timeFormate = $this->settingConfig->get('time_format');

        if ($timeFormate == '12h') {

            return 'hh:mm A';

        } else {

            return 'HH:mm';
        }
    }

    public function currencyCode()
    {
        $currency_code = $this->settingConfig->get('currency_code');

        return $currency_code;
    }

}