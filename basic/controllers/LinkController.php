<?php

namespace app\controllers;

use app\models\Shortener;
use app\models\ShortenerLogClicks;
use app\models\ShortenerLogIP;
use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use app\models\Country;

class LinkController extends Controller
{
    public function actionClick($shortCode)
    {
        $shortener = Shortener::findOne(['shortened' => $shortCode]);
        $shortenerId = $shortener ? $shortener->id : 0;
        $logClicks = ShortenerLogClicks::find()->where(['shortener_id' => $shortenerId])->one();
        if (!$logClicks) {
            $logClicks = new ShortenerLogClicks();
            $logClicks->shortener_id = $shortenerId;
            $logClicks->clicks = 1;
        } else {
            $logClicks->clicks = $logClicks->clicks + 1;
        }
        $logClicks->save();

        $logIP = new ShortenerLogIP();
        $logIP->shortener_id = $shortenerId;
        $logIP->ip_address = Yii::$app->request->userIP;
        $logIP->save();

        return $this->redirect($shortener->url);
    }
}

?>