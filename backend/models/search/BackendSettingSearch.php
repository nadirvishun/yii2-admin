<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\BackendSetting;

/**
 * BackendSettingSearch represents the model behind the search form about `backend\models\BackendSetting`.
 */
class BackendSettingSearch extends BackendSetting
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pid', 'type', 'sort', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['name', 'alias', 'value', 'extra', 'hint'], 'safe'],
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
        //todo，排序后续可能修改
        $query = BackendSetting::find()->orderBy(['sort' => SORT_ASC, 'id' => SORT_ASC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination' => [
                //'pageSize' => 20,
            //],
            //'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
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
            'pid' => $this->pid,
            'type' => $this->type,
            'sort' => $this->sort,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'extra', $this->extra])
            ->andFilterWhere(['like', 'hint', $this->hint]);

        return $dataProvider;
    }
}
