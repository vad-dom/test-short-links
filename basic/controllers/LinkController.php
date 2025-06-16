<?php

namespace app\controllers;

use app\models\Shortener;
use app\models\ShortenerLogIP;
use yii\web\HttpException;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class LinkController extends Controller
{
    /**
     * Переход по короткой ссылке
     *
     * @param string $id
     * @return Response
     * @throws HttpException
     */
    public function actionClick(string $id): Response
    {
        try {
            $shortener = Shortener::findOne(['shortened' => $id]);
            if (!$shortener) {
                throw new HttpException(404, 'Ссылка не найдена');
            }
            $shortener->clicks += 1;
            $shortener->save();

            $logIP = new ShortenerLogIP();
            $logIP->shortener_id = $shortener->id;
            $logIP->ip_address = Yii::$app->request->userIP;
            $logIP->save();

            return $this->redirect($shortener->url);
        } catch (\Exception $e) {
            throw new HttpException(404, 'Ошибка при переходе по ссылке: ' . $e->getMessage());
        }
    }
}

?>