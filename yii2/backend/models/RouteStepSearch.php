<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RouteStep;
use Yii;

/**
 * RouteStepSearch represents the model behind the search form of `app\models\RouteStep`.
 */
class RouteStepSearch extends RouteStep
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'route_id', 'city_id', 'type_transport', 'type_direction', 'type_day', 'active', 'version', 'user_id', 'status','pay'], 'integer'],
            [['name', 'number', 'alias', 'price', 'organization', 'info', 'time_work', 'route_text', 'temp_route_id', 'lastmod', 'marshruts_value', 'date'], 'safe'],
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
    public function search($params,$canView=0)
    {
      // var_dump($canView['userID']);// die();
        if ($canView['userID']) {
             $query = RouteStep::find()->where("user_id=".$canView['userID']);
        } else {
            $query = RouteStep::find();
        }

        // add conditions that should always apply here
        if (Yii::$app->user->can('admin'))  { $count=100; } else { $count=25;}
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $count,
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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'route_id' => $this->route_id,
            'city_id' => $this->city_id,
            'type_transport' => $this->type_transport,
            'type_direction' => $this->type_direction,
            'type_day' => $this->type_day,
            'active' => $this->active,
            'lastmod' => $this->lastmod,
            'version' => $this->version,
            'user_id' => $this->user_id,
            'date' => $this->date,
            'status' => $this->status,
            'pay' => $this->pay,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'price', $this->price])
            ->andFilterWhere(['like', 'organization', $this->organization])
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'time_work', $this->time_work])
            ->andFilterWhere(['like', 'route_text', $this->route_text])
            ->andFilterWhere(['like', 'temp_route_id', $this->temp_route_id])
            ->andFilterWhere(['like', 'marshruts_value', $this->marshruts_value])
            ->andFilterWhere(['like', 'pay', $this->pay]);

        return $dataProvider;
    }
}
