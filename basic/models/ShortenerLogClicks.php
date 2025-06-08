<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class ShortenerLogClicks extends ActiveRecord
{
    public static function tableName()
    {
        return 'shortener_log_clicks';
    }

    public function rules()
    {
        return [
            [['shortener_id', 'clicks'], 'required'],
            [['shortener_id', 'clicks'], 'integer'],
        ];
    }

}

?>