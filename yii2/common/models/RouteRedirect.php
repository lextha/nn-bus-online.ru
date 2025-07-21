<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "route_redirect".
 *
 * @property int $id
 * @property int $route_id
 * @property string $url
 * @property string $date
 */
class RouteRedirect extends \yii\db\ActiveRecord
{
    
        
    public function behaviors()
{
    return [
        'timestamp' => [
            'class' => TimestampBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => 'date',
                ActiveRecord::EVENT_BEFORE_UPDATE => 'date',
            ],
            'value' => function() { return date('Y-m-d H:i:s'); } // unix timestamp },
        ],
    ];
}
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'route_redirect';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['route_id', 'url'], 'required'],
            [['route_id'], 'integer'],
            [['date'], 'safe'],
            [['url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'route_id' => 'Route ID',
            'url' => 'Url',
            'date' => 'Date',
        ];
    }
}
