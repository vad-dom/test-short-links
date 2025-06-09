<?php

namespace app\models;

use DateTime;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class Shortener extends ActiveRecord
{
    private const OPTIONS = "NkAXITYvw3iObfKEaoeCUxqm2Dnrugl9PthVSM8yc7GQdBJHW6Lz4ZsjR15pF";

    public static function tableName(): string
    {
        return 'yii2_shortener';
    }

    public function rules()
    {
        return [
            [['url'], 'string', 'max' => 256],
            ['url', 'required', 'message' => 'Заполните поле'],
            ['url', 'url', 'message' => 'Некорректная ссылка'],
            ['url', 'isAvailable'],
            [['shortened'], 'string', 'max' => 16],
            [['shortened'], 'unique'],
        ];
    }

    public function isAvailable($attribute, $params)
    {
        $curlInit = curl_init($this->$attribute);
        curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($curlInit,CURLOPT_HEADER,true);
        curl_setopt($curlInit,CURLOPT_NOBODY,true);
        curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
        $response = curl_exec($curlInit);
        curl_close($curlInit);
        if (!$response) {
            $this->addError($attribute, 'Данный URL не доступен');
        }
    }

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