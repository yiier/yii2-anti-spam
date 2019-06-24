<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019/6/24 11:42 AM
 * description:
 */

namespace yier\antiSpam;

use yii\validators\Validator;
use yiier\antiSpam\models\Spam;


class SpamValidator extends Validator
{
    public $stringCompareScore = 0.5;

    public function validateAttribute($model, $attribute)
    {
        if (!$activeItems = Spam::getActiveItems()) {
            return true;
        }

        foreach ($activeItems as $activeItem) {
            if ($activeItem->type == Spam::TYPE_CONTAINS) {
                if (static::stringContains($activeItem->content, $model->{$attribute})) {
                    $this->addError($model, $attribute, $this->message ?: $attribute . 'is Spam');
                    break;
                }
            }

            if ($activeItem->type == Spam::TYPE_SIMILAR) {
                $totalScore = static::stringCompare($model->{$attribute}, $activeItem->content);
                if ($totalScore > $this->stringCompareScore) {
                    $this->addError($model, $attribute, $this->message ?: $attribute . 'is Spam');
                    break;
                }
            }
        }
    }

    /**
     *  similar text
     *
     * @param $strA
     * @param $strB
     * @return float|int
     */
    public static function stringCompare($strA, $strB)
    {
        $length = strlen($strA);
        $lengthB = strlen($strB);

        $i = 0;
        $segmentCount = 0;
        $segmentsInfo = [];
        $segment = '';
        while ($i < $length) {
            $char = substr($strA, $i, 1);
            if (strpos($strB, $char) !== false) {
                $segment = $segment . $char;
                if (strpos($strB, $segment) !== false) {
                    $segmentPosA = $i - strlen($segment) + 1;
                    $segmentPosB = strpos($strB, $segment);
                    $positionDiff = abs($segmentPosA - $segmentPosB);
                    $posFactor = ($length - $positionDiff) / $lengthB; // <-- ?
                    $lengthFactor = strlen($segment) / $length;
                    $segmentsInfo[$segmentCount] = [
                        'segment' => $segment,
                        'score' => ($posFactor * $lengthFactor)
                    ];
                } else {
                    $segment = '';
                    $i--;
                    $segmentCount++;
                }
            } else {
                $segment = '';
                $segmentCount++;
            }
            $i++;
        }

        // PHP 5.3 lambda in array_map
        $totalScore = array_sum(array_map(function ($v) {
            return $v['score'];
        }, $segmentsInfo));
        return $totalScore;
    }


    /**
     * contains validate
     *
     * @param string $rule eg:  网{2}赌
     * @param string $data
     * @return bool
     */
    public static function stringContains($rule, $data)
    {
        $key = preg_replace("/(\S+)\{(\d+)\}(\S+)/i", "$1.{0,$2}$3", $rule);
        preg_match_all("/($key)/i", $data, $match);
        if (empty($match[0])) {
            return true;
        }
        return false;
    }
}