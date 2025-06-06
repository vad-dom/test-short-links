<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\EntryForm;
use Da\QrCode\QrCode;

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
        //return $this->render('index');
        $model = new EntryForm();

        return $this->render('entry', ['model' => $model]);
    }

    public function actionSave()
    {
        //return $this->render('index');
        $model = new EntryForm();

        //echo '111';

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

    public function actionTest()
    {
        $form_model = new EntryForm();
        if(Yii::$app->request->isAjax){
            if ($form_model->load(Yii::$app->request->post()) && $form_model->validate()) {
                //return json_encode($form_model);
                $qqq = Yii::$app->getModule('shortener')->short($form_model->email);
                $qrCode = (new QrCode($form_model->email))
                    ->setSize(100);
                    //->setMargin(5);

                $qrCode->writeFile(__DIR__ . '/code.png');

                $www = Yii::$app->request->hostInfo . '/' . $qqq;

                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['model' => $form_model, 'qr' => $qrCode->writeDataUri(), 'www' => $www];
            }
            if ($form_model->hasErrors()) {
                throw new HttpException(400, $form_model->getFirstError('email'));
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
