<?php

namespace common\models;

use Yii;
use common\models\Station;

/**
 * This is the model class for table "stationmany".
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property int $city_id
 */
class Stationmany extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stationmany';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'alias', 'city_id'], 'required'],
            [['city_id'], 'integer'],
            [['name', 'alias'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'alias' => 'Alias',
            'city_id' => 'City ID',
        ];
    }
    
     public function getStations()
    {
        return $this->hasMany(Station::classname(), ['id' => 'station_id'])
            ->viaTable('stationmany_station', ['stationmany_id' => 'id']);
    }
    
}
