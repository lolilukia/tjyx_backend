<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Y3WpdVicR8vHK8zQF2yusDmJVReArR-t',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Enroll',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
    'controllerMap' => [
        'bind' => [
            'class' => 'app\controllers\BindController',
            'enableCsrfValidation' => false,
        ],
        'activity' => [
            'class' => 'app\controllers\ActivityController',
            'enableCsrfValidation' => false,
        ],
        'sign' => [
            'class' => 'app\controllers\SignController',
            'enableCsrfValidation' => false,
        ],
        'survey' => [
            'class' => 'app\controllers\SurveyController',
            'enableCsrfValidation' => false,
        ],
        'recharge' => [
            'class' => 'app\controllers\RechargeController',
            'enableCsrfValidation' => false,
        ],
        'info' => [
            'class' => 'app\controllers\InfoController',
            'enableCsrfValidation' => false,
        ],
        'feature' => [
            'class' => 'app\controllers\FeatureController',
            'enableCsrfValidation' => false,
        ],
        'coach' => [
            'class' => 'app\controllers\CoachController',
            'enableCsrfValidation' => false,
        ],
        'special' => [
            'class' => 'app\controllers\SpecialController',
            'enableCsrfValidation' => false,
        ]
    ],
    'modules' => [
        'activity' => 'app\modules\Activity',
        'enroll' => 'app\modules\Enroll',
        'member' => 'app\modules\Member',
        'signup' => 'app\modules\Signup',
        'survey' => 'app\modules\Survey',
        'recharge' => 'app\modules\Recharge',
        'feature' => 'app\modules\Feature',
        'coach' => 'app\modules\Coach',
        'special' => 'app\modules\Special'
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
