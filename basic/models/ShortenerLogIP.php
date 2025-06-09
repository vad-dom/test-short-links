<?php

namespace app\models;

use yii\db\ActiveRecord;

class ShortenerLogIP extends ActiveRecord
{
    public static function tableName()
    {
        return 'shortener_log_ip';
    }

    public function rules()
    {
        return [
            [['shortener_id', 'ip_address'], 'required'],
            ['shortener_id', 'integer'],
            ['ip_address', 'string'],
            ['created_at', 'safe'],
        ];
    }

}

?>