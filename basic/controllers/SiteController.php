<?php

namespace app\controllers;

use app\models\Shortener;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
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
     * Главная страница
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $model = new EntryForm();
        return $this->render('entry', ['model' => $model]);
    }

    /**
     * Создание короткой ссылки
     *
     * @return array
     */
    public function actionShorten(): array
    {
        $entry = new EntryForm();
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax || !$entry->load(Yii::$app->request->post())) {
            return ['ok' => false, 'errorMessage' => 'Непредвиденная ошибка. Мы уже работаем над этим'];
        }

        if (!$entry->validate()) {
            return ['ok' => false, 'errorMessage' => $entry->getFirstError('url')];
        }

        try {
            $shortener = new Shortener();
            $shortCode = $shortener->generateShortCode();
            $shortener->setAttributes([
                'url' => $entry->url,
                'shortened' => $shortCode
            ]);
            $shortener->save();

            $shortUrl = Yii::$app->urlManager->createAbsoluteUrl(['link/click', 'shortCode' => $shortCode]);
            $shortLinkText = Yii::$app->request->serverName . '/' . $shortCode;

            $qrCode = (new QrCode($shortUrl))
                ->setSize(100)
                ->setMargin(5);
            $qrCode->writeFile(__DIR__ . '/../web/img/qr_code.png');

            return [
                'ok' => true,
                'model' => $shortener,
                'qr_code' => $qrCode->writeDataUri(),
                'short_link' => $shortUrl,
                'short_link_text' => $shortLinkText,
                'short_code' => $shortCode,
            ];
        } catch (Exception $e) {
            return ['ok' => false, 'errorMessage' => 'Непредвиденная ошибка. Мы уже работаем над этим'];
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
}
