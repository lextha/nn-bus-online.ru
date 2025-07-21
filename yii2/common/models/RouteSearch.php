<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Route;

/**
 * RouteSearch represents the model behind the search form of `common\models\Route`.
 */
class RouteSearch extends Route
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'city_id', 'type_transport', 'type_direction', 'type_day', 'temp_route_id','version','active','has_time'], 'integer'],
            [['name', 'number', 'alias', 'price', 'organization', 'info', 'time_work', 'route_text'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params,$citis=0)
    {
        $query = Route::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort'=>[
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($citis AND !(\Yii::$app->user->can('admin'))) {
            $query->orWhere(['city_id'=>$citis]);
        }
            
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'city_id' => $this->city_id,
            'type_transport' => $this->type_transport,
            'type_direction' => $this->type_direction,
            'type_day' => $this->type_day,
            'temp_route_id' => $this->temp_route_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'price', $this->price])
            ->andFilterWhere(['like', 'organization', $this->organization])
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'time_work', $this->time_work])
            ->andFilterWhere(['like', 'version', $this->version])
            ->andFilterWhere(['like', 'active', $this->active])
            ->andFilterWhere(['like', 'route_text', $this->route_text])
             ->andFilterWhere(['like', 'has_time', $this->has_time]);

        return $dataProvider;
    }
}
