<?php

namespace app\models;

use Yii;
use yii\base\Model;

class EntryForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            ['email', 'required', 'message' => 'Заполните поле'],
            ['email', 'url', 'message' => 'Некорректная ссылка'],
            ['email', 'isAvailable'],
        ];
    }

    public function isAvailable($attribute, $params)
    {
        //$response = false;
/*        $temp = get_headers($this->email,true);
        if (!preg_match('/200/', $temp[0])) {
            $this->addError($attribute, 'Данный URL не доступен');
        }*/

        $curlInit = curl_init($this->$attribute);

        // Установка параметров запроса
        curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($curlInit,CURLOPT_HEADER,true);
        curl_setopt($curlInit,CURLOPT_NOBODY,true);
        curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

        // Получение ответа
        $response = curl_exec($curlInit);

        // закрываем CURL
        curl_close($curlInit);

        //return $response ? true : false;
        if (!$response) {
            $this->addError($attribute, 'Данный URL не доступен');
        }

    }
}

?>