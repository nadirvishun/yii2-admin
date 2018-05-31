<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AdminLog;
use kartik\daterange\DateRangeBehavior;

/**
 * AdminLogSearch represents the model behind the search form about `backend\models\AdminLog`.
 */
class AdminLogSearch extends AdminLog
{
    public $datetime_min;
    public $datetime_max;

    /**
     * 引入日期范围行为，主要是实现了分割最大最小
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'created_at',
                'dateStartAttribute' => 'datetime_min',
                'dateEndAttribute' => 'datetime_max',
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'admin_id', 'type'], 'integer'],
            [['created_at'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['title', 'model', 'controller', 'action', 'url_param', 'description', 'ip','datetime_max','datetime_min'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
    public function search($params)
    {
        $query = AdminLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
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
            'admin_id' => $this->admin_id,
            'type' => $this->type,
//            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'model', $this->model])
            ->andFilterWhere(['like', 'controller', $this->controller])
            ->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'url_param', $this->url_param])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['>=', 'created_at', $this->datetime_min])
            ->andFilterWhere(['<', 'created_at', $this->datetime_max]);

        return $dataProvider;
    }
}
