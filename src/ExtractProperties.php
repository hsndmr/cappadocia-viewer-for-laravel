<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer;

use ReflectionClass;
use Illuminate\Database\Eloquent\Model;

/**
 * https://github.com/laravel/telescope/blob/4.x/src/ExtractProperties.php.
 */
class ExtractProperties
{
    public static function from($target): array
    {
        return collect((new ReflectionClass($target))->getProperties())
            ->mapWithKeys(function ($property) use ($target) {
                $property->setAccessible(true);

                if (PHP_VERSION_ID >= 70400 && !$property->isInitialized($target)) {
                    return [];
                }

                if (($value = $property->getValue($target)) instanceof Model) {
                    return [$property->getName() => FormatModel::given($value)];
                } elseif (is_object($value)) {
                    return [
                        $property->getName() => [
                            'class'      => get_class($value),
                            'properties' => json_decode(json_encode($value), true),
                        ],
                    ];
                } else {
                    return [$property->getName() => json_decode(json_encode($value), true)];
                }
            })->toArray();
    }
}
