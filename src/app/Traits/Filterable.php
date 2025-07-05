<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait Filterable
{
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $this->applyFilters($query, $filters);
    }

    public function scopeSort(Builder $query, string $sort): Builder
    {
        return $this->applySorting($query, $sort);
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        $paginate = ['page', 'per_page'];
        foreach ($filters as $key => $value) {
            if (is_null($value) || $key == 'sort' || in_array($key, $paginate)) {
                continue;
            }

            $method = 'filterable' . Str::studly($key);
            if (method_exists($this, $method)) {
                $this->{$method}($value);

                continue;
            }

            if (Str::endsWith($key, '__in')) {
                $column = Str::before($key, '__in');
                if (Str::contains($column, '__')) {
                    $parts = explode('__', $column);
                    $relation = array_shift($parts);
                    $columnName = implode('__', $parts);
                    if (method_exists($this, $relation)) {
                        $query->whereHas($relation, function ($q) use ($columnName, $value) {
                            $q->whereIn($columnName, $value);
                        });
                    }
                } else {
                    $query->whereIn($column, explode(',', $value));
                }

                continue;
            }

            if ($key === 'search') {
                $this->applySearchFilter($query, $value);

                continue;
            }

            if ($this->applySpecialFunctionFilter($query, $key, $value)) {
                continue;
            }

            if (Str::contains($key, '__')) {
                $parts = explode('__', $key);
                $relation = array_shift($parts);
                $column = implode('__', $parts);
                if (method_exists($this, $relation)) {
                    $query->whereHas($relation, function ($q) use ($column, $value) {
                        $this->applyFilters($q, [$column => $value]);
                    });
                }

                continue;
            }

            $operatorMap = [
                '__gt' => '>',
                '__lt' => '<',
                '__gte' => '>=',
                '__lte' => '<=',
                '__eq' => '=',
                '__neq' => '!=',
            ];
            foreach ($operatorMap as $suffix => $operator) {
                if (Str::endsWith($key, $suffix)) {
                    $query->where(Str::before($key, $suffix), $operator, $value);

                    continue 2;
                }
            }

            if (Str::endsWith($key, '__range')) {
                $column = Str::before($key, '__range');
                $this->applyDateRangeFilter($query, $column, $value);

                continue;
            }

            if ($key === 'is_null' || $key === 'is_not_null') {
                $columns = explode(',', $value);
                foreach ($columns as $column) {
                    if (Str::contains($column, '__')) {
                        $parts = explode('__', $column);
                        $relation = array_shift($parts);
                        $columnName = implode('__', $parts);

                        if (method_exists($this, $relation)) {
                            $query->whereHas($relation, function ($q) use ($columnName, $key) {
                                $key === 'is_null' ? $q->whereNull($columnName) : $q->whereNotNull($columnName);
                            });
                        }
                    } else {
                        $key === 'is_null' ? $query->whereNull($column) : $query->whereNotNull($column);
                    }
                }

                continue;
            }

            $query->where($key, $value);
        }

        return $query;
    }

    private function applyDateRangeFilter(Builder $query, string $column, string $range): void
    {
        $now = Carbon::now();

        $ranges = [
            'today' => [$now->startOfDay(), $now->endOfDay()],
            'yesterday' => [$now->subDay()->startOfDay(), $now->subDay()->endOfDay()],
            'this_week' => [$now->startOfWeek(), $now->endOfWeek()],
            'last_week' => [$now->subWeek()->startOfWeek(), $now->subWeek()->endOfWeek()],
            'last_7_days' => [Carbon::today()->subDays(6), Carbon::today()],
            'last_30_days' => [Carbon::today()->subDays(29), Carbon::today()],
            'this_month' => [$now->startOfMonth(), $now->endOfMonth()],
            'last_month' => [$now->subMonth()->startOfMonth(), $now->subMonth()->endOfMonth()],
            'this_year' => [$now->startOfYear(), $now->endOfYear()],
            'last_year' => [$now->subYear()->startOfYear(), $now->subYear()->endOfYear()],
        ];

        if (isset($ranges[$range])) {
            $query->whereBetween($column, $ranges[$range]);
        }
    }

    private function applySpecialFunctionFilter(Builder $query, string $key, string $value): bool
    {
        $functionMap = [
            '__lower' => 'LOWER',
            '__upper' => 'UPPER',
            '__length' => 'LENGTH',
            '__trim' => 'TRIM',
            '__date' => 'DATE',
            '__month' => 'MONTH',
            '__year' => 'YEAR',
        ];

        foreach ($functionMap as $suffix => $function) {
            if (Str::endsWith($key, $suffix)) {
                $column = Str::before($key, $suffix);
                $query->where(DB::raw("{$function}($column)"), $value);

                return true;
            }
        }

        return false;
    }

    private function applySorting(Builder $query, string $sort): Builder
    {
        $sortFields = explode(',', $sort);

        foreach ($sortFields as $field) {
            $direction = Str::startsWith($field, '-') ? 'desc' : 'asc';
            $column = ltrim($field, '-');

            if (Str::contains($column, '__')) {
                $parts = explode('__', $column);
                $relation = array_shift($parts);
                $column = implode('__', $parts);
                if (method_exists($this, $relation)) {
                    $query->with([$relation => function ($q) use ($column, $direction) {
                        $q->orderBy($column, $direction);
                    }]);
                }
            } else {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }

    private function applySearchFilter(Builder $query, string $searchValue): void
    {
        $searchFields = property_exists($this, 'searchable') ? $this->searchable : [];

        $query->where(function ($q) use ($searchFields, $searchValue) {
            foreach ($searchFields as $field) {
                if (Str::contains($field, '.')) {
                    $parts = explode('.', $field);
                    $relation = array_shift($parts);
                    $column = implode('.', $parts);

                    if (method_exists($this, $relation)) {
                        $q->orWhereHas($relation, function ($subQuery) use ($column, $searchValue) {
                            $subQuery->where($column, 'LIKE', "%{$searchValue}%");
                        });
                    }
                } else {
                    $q->orWhere($field, 'LIKE', "%{$searchValue}%");
                }
            }
        });
    }
}
