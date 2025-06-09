<?php

namespace app\models;

use yii\base\Model;

class EntryForm extends Model
{
    public $url;

    public function rules()
    {
        return [
            ['url', 'required', 'message' => 'Заполните поле'],
            ['url', 'url', 'message' => 'Некорректная ссылка'],
            ['url', 'string', 'max' => 256],
            ['url', 'isAvailable'],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function isAvailable($attribute, $params): bool
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
        return true;
    }
}

?>