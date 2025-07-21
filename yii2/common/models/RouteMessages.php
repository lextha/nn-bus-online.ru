<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "route_messages".
 *
 * @property int $id
 * @property int $route_id
 * @property string $text
 * @property string $photo
 */
class RouteMessages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'route_messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['route_id'], 'required'],
            [['route_id'], 'integer'],
            [['text', 'photo','text'], 'string'],
            [['status'], 'safe'],
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
            'text' => 'Текст сообщения',
            'photo' => 'Photo',
            'status' => 'Статус',
        ];
    }
}
