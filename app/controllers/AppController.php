<?php
namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Base controller that is intended to be inherited by all app controllers.
 *
 * @author Gani Georgiev <gani.georgiev@gmail.com>
 */
class AppController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    $this->redirect(['site/entrance']);
                }
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $request     = Yii::$app->getRequest();;
        $requestLang = $request->get('lang', null);
        $lang        = 'en';

        if (!$requestLang) {
            // auto detect
            $countryCode = strtolower(Yii::$app->geoip->lookupCountryCode());

            if ($countryCode === 'bg') {
                $lang = 'bg';
            } elseif ($countryCode === 'pl') {
                $lang = 'pl';
            } elseif ($countryCode === 'br' || $countryCode === 'pt' || $countryCode === 'pt-br') {
                $lang = 'pt-br';
            } else {
                $lang = 'en';
            }
        } elseif (isset(Yii::$app->params['languages'][$requestLang])) {
            $lang = $requestLang;
        }

        if ($lang !== $requestLang && !$request->isAjax) {
            $this->redirect(Url::current(['lang' => $lang]));
        }

        Yii::$app->language = Yii::$app->params['languages'][$lang];

        return parent::beforeAction($action);
    }
}
