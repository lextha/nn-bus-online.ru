<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "route_source".
 *
 * @property int $id
 * @property int $route_id
 * @property string $text
 * @property string $date
 */
class RouteSource extends \yii\db\ActiveRecord
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
        return 'route_source';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['route_id', 'text'], 'required'],
            [['route_id'], 'integer'],
            [['text'], 'string'],
            [['date'], 'safe'],
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
            'text' => 'Text',
            'date' => 'Date',
        ];
    }
}
