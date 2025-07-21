<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "map_rout".
 *
 * @property int $id
 * @property int $route_id
 * @property string $line
 * @property int $direction
 * @property int $active
 */
class MapRout extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'map_rout';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['route_id', 'line', 'direction', 'active'], 'required'],
            [['route_id', 'direction', 'active'], 'integer'],
            [['line'], 'string'],
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
            'line' => 'Line',
            'direction' => 'Direction',
            'active' => 'Active',
        ];
    }
}
