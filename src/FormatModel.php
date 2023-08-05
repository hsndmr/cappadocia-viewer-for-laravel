<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer;

use BackedEnum;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * https://github.com/laravel/telescope/blob/4.x/src/FormatModel.php.
 */
class FormatModel
{
    public static function given($model): string
    {
        if ($model instanceof Pivot && !$model->incrementing) {
            $keys = [
                $model->getAttribute($model->getForeignKey()),
                $model->getAttribute($model->getRelatedKey()),
            ];
        } else {
            $keys = $model->getKey();
        }

        return get_class($model).':'.implode('_', array_map(function ($value) {
            return $value instanceof BackedEnum ? $value->value : $value;
        }, Arr::wrap($keys)));
    }
}
