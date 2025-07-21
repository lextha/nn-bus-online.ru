<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "time_work".
 *
 * @property int $station_rout_id
 * @property string $monday
 * @property string $tuesday
 * @property string $wednesday
 * @property string $thursday
 * @property string $friday
 * @property string $saturday
 * @property string $sunday
 */
class TimeWork extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'time_work';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['station_rout_id'], 'required'],
            [['station_rout_id'], 'integer'],
            [['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'], 'string'],
            [['station_rout_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'station_rout_id' => 'Station Rout ID',
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];
    }
    public static function getTimeWork($station_rout_id) {
        $row = Yii::$app->db->createCommand('SELECT * FROM time_work WHERE station_rout_id='.$station_rout_id.'')->queryOne();
        return $row;
    }
    
    public static function getTimeWorkStep($station_rout_id,$id) {
        $row = Yii::$app->db->createCommand('SELECT * FROM time_work_step WHERE station_rout_id='.$station_rout_id.' AND route_step_id='.$id.'')->queryOne();
        return $row;
    }
}
