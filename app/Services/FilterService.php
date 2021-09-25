<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Service filter list
 */
class FilterService
{

    var $builder;
    var $apply;

    public static $attributesAlias = [
        'title' => 'title',
        'likes' => 'likes_count',
        'actives' => 'availability',
        'stock' => 'stock'
    ];

    /**
     * @param Illuminate\Database\Eloquent\Builder $builder
     * @param boolean $appy, if apply or not filter or sort
     */
    private function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public static function createFilter(Builder $builder)
    {
        return new FilterService($builder);
    }

    /**
     * @param string $param - query url param
     * @param string value - new value to param
     */
    public function defaultSort($param, $value)
    {
        if (!request($param)) request()->merge([$param => $value]);
        return $this;
    }

    /**
     * sort list asc or desc
     * @param string $param - url query param
     */
    public function sort($param, $apply = true)
    {
        if (!$apply) return $this;
        $sort = request($param);
        if (!$sort) return $this;
        $columnName = $sort;
        $ascDesc = "asc";
        if (Str::is('-*', $sort)) {
            $columnName = Str::substr($sort, 1);
            $ascDesc = "desc";
        }

        $columnName = self::$attributesAlias[$columnName] ?? null;
        if (!$columnName) return $this->builder;

        $this->builder->reorder($columnName, $ascDesc);
        return $this;
    }

    /**
     * Filter list by column
     * @param string $param = url query param
     * @param string $operator - to use in where clausule
     * @param boolean $isBoolean - is param value is a boolean
     */
    public function filter($param, $operator = "=", $isBoolean = false, $apply = true)
    {
        if (!$apply) return $this;
        if (!$value = request($param)) return $this;
        $columnValue = $isBoolean ? request()->boolean($param) : $value;
        $columnKey = self::$attributesAlias[$param];
        $this->builder->where($columnKey, $operator, $columnValue);
        return $this;
    }

    public function search($param, $apply = true)
    {
        if (!$apply) return $this;
        if (!$value = request($param)) return $this;
        $columnKey = self::$attributesAlias[$param];
        $this->builder->where($columnKey, 'like', "%$value%");
        return $this;
    }

    public function getBuilder()
    {
        return $this->builder;
    }
}
