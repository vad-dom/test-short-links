<?php

namespace app\models;

use DateTime;
use yii\db\ActiveRecord;

class Shortener extends ActiveRecord
{
    private const OPTIONS = "NkAXITYvw3iObfKEaoeCUxqm2Dnrugl9PthVSM8yc7GQdBJHW6Lz4ZsjR15pF";

    public static function tableName(): string
    {
        return 'shortener';
    }

    public function rules()
    {
        return [
            ['url', 'string', 'max' => 256],
            ['shortened', 'string', 'max' => 16],
            ['shortened', 'unique'],
            ['clicks', 'integer'],
            ['clicks', 'default', 'value' => 0],
        ];
    }

    /**
     * Генерация кода для короткой ссылки
     *
     * @return string
     */
    public function generateShortCode(): string
    {
        $date = new DateTime();
        $monthYear = (int) $date->format('y') + (int) $date->format('n');
        $dayHour = (int) $date->format('j') + (int) $date->format('G');
        $code = [
            self::OPTIONS[$monthYear],
            self::OPTIONS[$dayHour],
            self::OPTIONS[array_sum(mb_str_split($date->format('u')))],
            self::OPTIONS[(int) ($date->format('s'))],
        ];
        return join("", $code);
    }
}

?>