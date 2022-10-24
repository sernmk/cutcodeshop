<?php

declare(strict_types=1);

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Model;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function (Model $model) {
            $model->slug = $model->slug
                ?? str($model->{self::slugFrom()})
                    ->append(time())// TODO ДЗ - добавить инкремент, если получаемый slug уже есть в бд, например, ruchka, ruchka-1
                    ->slug(); // TODO зачем тут фигурные скобки? (без них ошибка типа данных)
        });
    }

    // Можно переопределить в модели возвращаемую строку, например "name", если нужно формировать slug из name
    public static function slugFrom(): string
    {
        return 'title';
    }
}
