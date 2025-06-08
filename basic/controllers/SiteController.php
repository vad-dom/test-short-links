<?php

namespace app\controllers;

use app\models\ShortenerLogClicks;
use app\models\ShortenerLogIP;
use eseperio\shortener\models\Shortener;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\EntryForm;
use Da\QrCode\QrCode;
use yii\helpers\Url;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new EntryForm();
        return $this->render('entry', ['model' => $model]);
    }

    public function actionShorten()
    {
        $entry = new EntryForm();
        if (Yii::$app->request->isAjax) {
            if ($entry->load(Yii::$app->request->post()) && $entry->validate()) {
                $shortCode = Yii::$app->getModule('shortener')->short($entry->url);
                $qrCode = (new QrCode($entry->url))
                    ->setSize(100)
                    ->setMargin(5);

                $qrCode->writeFile(__DIR__ . '/../web/img/qr_code.png');

                $shortLink = Yii::$app->request->hostInfo . '/' . $shortCode;
                $shortLinkText = Yii::$app->request->serverName . '/' . $shortCode;

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'model' => $entry,
                    'qr_code' => $qrCode->writeDataUri(),
                    'short_link' => $shortLink,
                    'short_link_text' => $shortLinkText,
                    'short_code' => $shortCode,
                ];
            }
            if ($entry->hasErrors()) {
                throw new HttpException(400, $entry->getFirstError('url'));
            }
            return 'Запрос принят!';
        }
    }

    public function actionClick()
    {
        if (Yii::$app->request->isAjax) {
            $shortCode = Yii::$app->request->post('short_code');
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

            if ($logClicks->hasErrors()) {
                throw new HttpException(400, $logClicks->getFirstError('shortener_id'));
            }

            return true;
        }
/*        if ($entry->hasErrors()) {
            throw new HttpException(400, $entry->getFirstError('url'));
        }
        return 'Запрос принят!';*/
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSay($message = 'Hello')
    {
        return $this->render('say', ['message' => $message]);
    }
}
