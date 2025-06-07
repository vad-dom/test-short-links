<?php

namespace app\controllers;

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

    public function actionSave()
    {
        $model = new EntryForm();

        $p = Yii::$app->request->post();
        print_r($p);

        if ($model->load($p) && $model->validate()) {
            // valid data received in $model

            // do something meaningful here about $model ...
            $qqq = Yii::$app->getModule('shortener')->short($model->email);

            //$qr = Yii::$app->get('qr');

            //Yii::$app->response->format = Response::FORMAT_RAW;
            //Yii::$app->response->headers->add('Content-Type', $qr->getContentType());

            $qrCode = (new QrCode($model->email))
                ->setSize(250);
                //->setMargin(5);

            $qrCode->writeFile(__DIR__ . '/code.png');

            //$www =Yii::$app->getModule('shortener')->expand($qqq);
            $www = Yii::$app->request->hostInfo . '/' . $qqq;

            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true, 'model' => $model, 'qr' => $qrCode->writeDataUri(), 'www' => $www];

            //return $this->render('entry-confirm', ['model' => $model, 'qr' => $qrCode->writeDataUri(), 'www' => $www]);
        }
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
                ];
            }
            if ($entry->hasErrors()) {
                throw new HttpException(400, $entry->getFirstError('url'));
            }
            return 'Запрос принят!';
        }
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

    public function actionEntry()
    {
        $model = new EntryForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // valid data received in $model

            // do something meaningful here about $model ...
            $qqq = Yii::$app->getModule('shortener')->short($model->email);

            //$qr = Yii::$app->get('qr');

            //Yii::$app->response->format = Response::FORMAT_RAW;
            //Yii::$app->response->headers->add('Content-Type', $qr->getContentType());

            $qrCode = (new QrCode($model->email))
                ->setSize(250)
                ->setMargin(5);

            $qrCode->writeFile(__DIR__ . '/code.png');

            //$www =Yii::$app->getModule('shortener')->expand($qqq);
            $www = Yii::$app->request->hostInfo . '/' . Yii::$app->getModule('shortener')->urlConvert($qqq);

            return $this->render('entry-confirm', ['model' => $model, 'qr' => $qrCode->writeDataUri(), 'www' => $www]);
        } else {
            // either the page is initially displayed or there is some validation error
            return $this->render('entry', ['model' => $model]);
        }
    }
}
