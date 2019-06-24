<?php

namespace yiier\antiSpam\models;

/**
 * This is the model class for table "{{%spam}}".
 *
 * @property int $id
 * @property int $status 0 or 1
 * @property string $content
 * @property string $type contains or similar
 * @property string $for
 */
class Spam extends \yii\db\ActiveRecord
{
    /**
     * @var int active
     */
    const STATUS_ACTIVE = 1;

    /**
     * @var int unactivated
     */
    const STATUS_UNACTIVATED = 0;

    /**
     * @var string contains
     */
    const TYPE_CONTAINS = 'contains';

    /**
     * @var string similar
     */
    const TYPE_SIMILAR = 'similar';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%spam}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['content'], 'required'],
            [['content'], 'string'],
            [['type', 'for'], 'string', 'max' => 20],
            [['status'], 'in' => [self::STATUS_ACTIVE, self::STATUS_UNACTIVATED]],
            [['type'], 'in' => [self::TYPE_SIMILAR, self::TYPE_CONTAINS]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'content' => 'Content',
            'type' => 'Type',
            'for' => 'For',
        ];
    }


    /**
     * @param string $for
     * @return array|\yii\db\ActiveRecord[]|Spam[]
     */
    public static function getActiveItems($for = 'all')
    {
        return Spam::find()->where(['status' => Spam::STATUS_ACTIVE, 'for' => $for])->all();
    }


    /**
     * @param $type
     * @param $content
     */
    public static function create($type, $content)
    {
        $model = new Spam();
        $model->type = $type;
        $model->content = $content;
        $model->save();
    }
}
