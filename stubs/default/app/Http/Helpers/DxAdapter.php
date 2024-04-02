<?php

namespace App\Http\Helpers;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DxAdapter
{
    protected $stringSort = "";
    protected $queryBuilder;
    protected $request;
    protected $take;
    protected $selectedFields = null;

    public function __construct(Builder $queryBuilder, Request $request = null)
    {
        $this->queryBuilder = $queryBuilder;

        if ($request == null) {
            $this->request = request();
        } else {
            $this->request = $request;
        }
    }

    static public function load(Builder $queryBuilder, Request $request = null)
    {
        $instance = new self($queryBuilder, $request);
        return $instance->process();
    }

    static public function load_without_filter(Builder $queryBuilder, Request $request = null)
    {
        $instance = new self($queryBuilder, $request);
        return $instance->process_without_filter();
    }

    private function parseSelect()
    {
        if ($this->request->has("select")) {
            $this->selectedFields = json_decode($this->request->select);
            $this->queryBuilder->select($this->selectedFields);
        }
    }

    /**
     * Support most common dxdatagrid filter
     * ex [['name', 'contains', 'john'], 'and', ['email', 'contains', 'gmail']]
     */
    private function parseFilter()
    {

        if ($this->request->has("filter")) {
            $requestFilter = json_decode($this->request->filter);

            // Force single field Filter to array format
            if (!is_array($requestFilter[0])) {
                $requestFilter = [$requestFilter];
            }

            $conjungtion = null;
            $this->parseFilterRecursively($requestFilter, $conjungtion);
        }

        return $this;
    }

    public function parseFilterRecursively($item, &$conjungtion)
    {
        collect($item)->each(function ($item) use (&$conjungtion) {
            if (!is_array($item)) {
                $conjungtion = $item;
                return true;
            }

            if ((self::is_multidimensional($item))) {
                $this->parseFilterRecursively($item, $conjungtion);
            } else {
                list($field, $condition, $value) = $item;

                if (strpos($field, ".") !== false) {
                    $this->relationFilter($item, $conjungtion);
                } else {
                    $isDateFormat = (strlen($value) > 1 && date('Y-m-d', strtotime($value)) === $value);
                    $isNumeric = (is_numeric($value));
                    $isBoolean = (is_bool($value));
                    if ($isDateFormat) {
                        $this->applyDateFilter($item, $conjungtion);
                    } else if ($isNumeric) {
                        $this->applyNumericFilter($item, $conjungtion);
                    } else if ($isBoolean) {
                        $this->applyBooleanFilter($item, $conjungtion);
                    } else {
                        $this->applyStringFilter($item, $conjungtion);
                    }
                }
            }
        });
    }

    static function is_multidimensional(array $array)
    {
        return count($array) !== count($array, COUNT_RECURSIVE);
    }

    private function applySort()
    {

        if ($this->request->has('sort')) {
            $sort = json_decode($this->request->get('sort'));
            collect($sort)->each(function ($value) {
                if ($value->desc) {
                    $this->queryBuilder->orderBy($value->selector, 'desc');
                } else {
                    $this->queryBuilder->orderBy($value->selector, 'asc');
                }
            });
        }

        return $this;
    }

    private function applyStringFilter($item, $conjungtion)
    {
        list($field, $condition, $value) = $item;

        switch ($conjungtion) {
            case '!':
                switch ($condition) {
                    case 'contains':
                        $this->queryBuilder->orWhere($field, 'not like', "%$value%");
                        break;
                    case 'notcontains':
                        $this->queryBuilder->orWhere($field, 'like', "%$value%");
                        break;
                    case 'startswith':
                        $this->queryBuilder->orWhere($field, 'not like', "$value%");
                        break;
                    case 'endswith':
                        $this->queryBuilder->orWhere($field, 'not like', "%$value");
                        break;
                    case '=':
                        $this->queryBuilder->orWhere($field, '<>', "$value");
                        break;
                    case '<>':
                        $this->queryBuilder->orWhere($field, '=', "$value");
                        break;
                    default:
                        throw new Exception("Error Processing Request");
                        break;
                }
                break;
            case 'or':
                switch ($condition) {
                    case 'contains':
                        $this->queryBuilder->orWhere($field, 'like', "%$value%");
                        break;
                    case 'notcontains':
                        $this->queryBuilder->orWhere($field, 'not like', "%$value%");
                        break;
                    case 'startswith':
                        $this->queryBuilder->orWhere($field, 'like', "$value%");
                        break;
                    case 'endswith':
                        $this->queryBuilder->orWhere($field, 'like', "%$value");
                        break;
                    case '=':
                        $this->queryBuilder->orWhere($field, '=', "$value");
                        break;
                    case '<>':
                        $this->queryBuilder->orWhere($field, '<>', "$value");
                        break;
                    default:
                        throw new Exception("Error Processing Request");
                        break;
                }
            default:
                switch ($condition) {
                    case 'contains':
                        $this->queryBuilder->where($field, 'like', "%$value%");
                        break;
                    case 'notcontains':
                        $this->queryBuilder->where($field, 'not like', "%$value%");
                        break;
                    case 'startswith':
                        $this->queryBuilder->where($field, 'like', "$value%");
                        break;
                    case 'endswith':
                        $this->queryBuilder->where($field, 'like', "%$value");
                        break;
                    case '=':
                        $this->queryBuilder->where($field, '=', "$value");
                        break;
                    case '<>':
                        $this->queryBuilder->where($field, '<>', "$value");
                        break;
                    default:
                        throw new Exception("Error Processing Request");
                        break;
                }
                break;
        }
    }

    private function applyDateFilter($item)
    {
        list($field, $condition, $value) = $item;
        $value = date('Y-m-d', strtotime($value));
        switch ($condition) {
            case '=':
                $this->queryBuilder->where($field, '=', $value);
            case '<':
                $this->queryBuilder->whereDate($field, '<', $value);
                break;
            case '<=':
                $this->queryBuilder->whereDate($field, '<=', $value);
                break;
            case '>':
                $this->queryBuilder->whereDate($field, '>', $value);
                break;
            case '>=':
                $this->queryBuilder->whereDate($field, '>=', $value);
                break;
            default:
                throw new Exception("Error Processing Request");
                break;
        }
    }

    private function applyNumericFilter($item, $conjungtion)
    {
        list($field, $condition, $value) = $item;
        switch ($conjungtion) {
            case '!':
                switch ($condition) {
                    case '=':
                        $this->queryBuilder->where($field, '<>', $value);
                        break;
                    case '<':
                        $this->queryBuilder->where($field, '>', $value);
                        break;
                    case '<=':
                        $this->queryBuilder->where($field, '>=', $value);
                        break;
                    case '>':
                        $this->queryBuilder->where($field, '<', $value);
                        break;
                    case '>=':
                        $this->queryBuilder->where($field, '<=', $value);
                        break;
                    case 'contains':
                        $this->queryBuilder->where($field, 'not like', "%$value%");
                        break;
                    case 'notcontains':
                        $this->queryBuilder->where($field, 'like', "%$value%");
                        break;
                    case 'startswith':
                        $this->queryBuilder->where($field, 'not like', "$value%");
                        break;
                    case 'endswith':
                        $this->queryBuilder->where($field, 'not like', "%$value");
                        break;
                    case '<>':
                        $this->queryBuilder->where($field, '=', "$value");
                        break;
                    default:
                        throw new Exception("Error Processing Request");
                        break;
                }
                break;
            case 'or':
                switch ($condition) {
                    case '=':
                        $this->queryBuilder->orWhere($field, '=', $value);
                        break;
                    case '<':
                        $this->queryBuilder->orWhere($field, '<', $value);
                        break;
                    case '<=':
                        $this->queryBuilder->orWhere($field, '<=', $value);
                        break;
                    case '>':
                        $this->queryBuilder->orWhere($field, '>', $value);
                        break;
                    case '>=':
                        $this->queryBuilder->orWhere($field, '>=', $value);
                        break;
                    case 'contains':
                        $this->queryBuilder->orWhere($field, 'like', "%$value%");
                        break;
                    case 'notcontains':
                        $this->queryBuilder->orWhere($field, 'not like', "%$value%");
                        break;
                    case 'startswith':
                        $this->queryBuilder->orWhere($field, 'like', "$value%");
                        break;
                    case 'endswith':
                        $this->queryBuilder->orWhere($field, 'like', "%$value");
                        break;
                    case '<>':
                        $this->queryBuilder->orWhere($field, '<>', "$value");
                        break;
                    default:
                        throw new Exception("Error Processing Request");
                        break;
                }
                break;
            default:
                switch ($condition) {
                    case '=':
                        $this->queryBuilder->where($field, '=', $value);
                        break;
                    case '<':
                        $this->queryBuilder->where($field, '<', $value);
                        break;
                    case '<=':
                        $this->queryBuilder->where($field, '<=', $value);
                        break;
                    case '>':
                        $this->queryBuilder->where($field, '>', $value);
                        break;
                    case '>=':
                        $this->queryBuilder->where($field, '>=', $value);
                        break;
                    case 'contains':
                        $this->queryBuilder->where($field, 'like', "%$value%");
                        break;
                    case 'notcontains':
                        $this->queryBuilder->where($field, 'not like', "%$value%");
                        break;
                    case 'startswith':
                        $this->queryBuilder->where($field, 'like', "$value%");
                        break;
                    case 'endswith':
                        $this->queryBuilder->where($field, 'like', "%$value");
                        break;
                    case '<>':
                        $this->queryBuilder->where($field, '<>', "$value");
                        break;
                    default:
                        throw new Exception("Error Processing Request");
                        break;
                }
                break;
        }
    }

    private function applyBooleanFilter($item)
    {
        list($field, $condition, $value) = $item;

        switch ($value) {
            case true:
                $this->queryBuilder->where($field, '=', 1);
                break;
            case false:
                $this->queryBuilder->where($field, '=', 0);
                break;
            default:
                throw new Exception("Error Processing Request");
                break;
        }
    }

    public function process()
    {
        $this->parseSelect();
        $this->parseFilter();
        $this->applySort();
        $this->setNextPagePaginate();

        return $this->queryBuilder;
    }

    public function process_without_filter()
    {
        $this->parseSelect();
        $this->applySort();
        $this->setNextPagePaginate();

        return $this->queryBuilder;
    }

    public function requirePagination()
    {
        return $this->request->has("take") && $this->request->has('skip');
    }

    public function setNextPagePaginate()
    {
        if ($this->requirePagination()) {
            if ((int)$this->request->skip > 0) {
                $this->request->merge([
                    "page" => $this->request->skip / $this->request->take + 1
                ]);
            }
        }
    }

    public function relationFilter($item, $conjungtion)
    {
        list($field, $condition, $value) = $item;

        $isDateFormat = (strlen($value) > 1 && date('Y-m-d', strtotime($value)) === $value);
        $isNumeric = (is_numeric($value));
        $isBoolean = (is_bool($value));

        if ($isDateFormat) {
            $this->applyDateFilterRelation($item, $conjungtion);
        } else if ($isNumeric) {
            $this->applyNumericFilterRelation($item, $conjungtion);
        } else if ($isBoolean) {
            $this->applyBooleanFilterRelation($item, $conjungtion);
        } else {
            $this->applyStringFilterRelation($item, $conjungtion);
        }
    }

    protected function applyStringFilterRelation($item, $conjungtion)
    {
        list($field, $condition, $value) = $item;
        $relation = explode('.', $field);
        $fieldName = array_pop($relation);
        $has = implode(".", $relation);

        switch ($conjungtion) {
            case '!':
                switch ($condition) {
                    case 'contains':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, 'not like', "%$value%");
                        });
                        break;
                    case 'notcontains':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, 'not like', "%$value$");
                        });
                        break;
                    case 'startswith':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, 'not like', "$value%");
                        });
                        break;
                    case 'endswith':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, 'not like', "%$value");
                        });
                        break;
                    case '=':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '<>', "$value");
                        });
                        break;
                    case '<>':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '=', "$value");
                        });
                        break;
                    default:
                        throw new Exception("Error Processing Request");
                        break;
                }
                break;
            case 'or':
                switch ($condition) {
                    case 'contains':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->orWhere($fieldName, 'like', "%$value%");
                        });
                        break;
                    case 'notcontains':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->orWhere($fieldName, 'not like', "%$value%");
                        });
                        break;
                    case 'startswith':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->orWhere($fieldName, 'like', "$value%");
                        });
                        break;
                    case 'endswith':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->orWhere($fieldName, 'like', "%$value");
                        });
                        break;
                    case '=':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->orWhere($fieldName, '=', "$value");
                        });
                        break;
                    case '<>':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->orWhere($fieldName, '<>', "$value");
                        });
                        break;
                    default:
                        throw new Exception("Error Processing Request");
                        break;
                }
            default:
                switch ($condition) {
                    case 'contains':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, 'like', "%$value%");
                        });
                        break;
                    case 'notcontains':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, 'not like', "%$value%");
                        });
                        break;
                    case 'startswith':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, 'like', "$value%");
                        });
                        break;
                    case 'endswith':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, 'like', "%$value");
                        });
                        break;
                    case '=':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '=', "$value");
                        });
                        break;
                    case '<>':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '<>', "$value");
                        });
                        break;
                    default:
                        throw new Exception("Error Processing Request");
                        break;
                }
                break;
        }
    }

    protected function applyDateFilterRelation($item)
    {
        list($field, $condition, $value) = $item;
        $relation = explode('.', $field);
        $fieldName = array_pop($relation);
        $has = implode(".", $relation);

        switch ($condition) {
            case '=':
                $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                    $query->where($fieldName, '=', "$value");
                });
                break;
            case '<':
                $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                    $value = date('Y-m-d', strtotime($value));
                    $query->whereDate($fieldName, '<', $value);
                });
                break;
            case '<=':
                $this->queryBuilder->where($has, function ($query) use ($fieldName, $value) {
                    $value = date('Y-m-d', strtotime($value));
                    $query->whereDate($fieldName, '<=', $value);
                });
                break;
            case '>':
                $this->queryBuilder->where($has, function ($query) use ($fieldName, $value) {
                    $value = date('Y-m-d', strtotime($value));
                    $query->whereDate($fieldName, '>', $value);
                });
                break;
            case '>=':
                $this->queryBuilder->where($has, function ($query) use ($fieldName, $value) {
                    $value = date('Y-m-d', strtotime($value));
                    $query->whereDate($fieldName, '>=', $value);
                });
                break;
            default:
                throw new Exception("Error Processing Request");
                break;
        }
    }

    protected function applyNumericFilterRelation($item, $conjungtion)
    {
        list($field, $condition, $value) = $item;
        $relation = explode('.', $field);
        $fieldName = array_pop($relation);
        $has = implode(".", $relation);
        switch ($conjungtion) {
            case '!':
                switch ($condition) {
                    case '=':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '<>', "$value");
                        });
                        break;
                    case '<':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '>', "$value");
                        });
                        break;
                    case '<=':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '>=', "$value");
                        });
                        break;
                    case '>':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '<', "$value");
                        });
                        break;
                    case '>=':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '<=', "$value");
                        });
                        break;
                    default:
                        throw new Exception("Error Processing Request");
                        break;
                }
                break;
            case 'or':
                switch ($condition) {
                    case '=':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->orWhere($fieldName, '=', "$value");
                        });
                        break;
                    case '<':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->orWhere($fieldName, '<', "$value");
                        });
                        break;
                    case '<=':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->orWhere($fieldName, '<=', "$value");
                        });
                        break;
                    case '>':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->orWhere($fieldName, '>', "$value");
                        });
                        break;
                    case '>=':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->orWhere($fieldName, '>=', "$value");
                        });
                        break;
                    default:
                        throw new Exception("Error Processing Request");
                        break;
                }
                break;
            default:
                switch ($condition) {
                    case '=':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '=', "$value");
                        });
                        break;
                    case '<':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '<', "$value");
                        });
                        break;
                    case '<=':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '<=', "$value");
                        });
                        break;
                    case '>':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '>', "$value");
                        });
                        break;
                    case '>=':
                        $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                            $query->where($fieldName, '>=', "$value");
                        });
                        break;
                    default:
                        throw new Exception("Error Processing Request");
                        break;
                }
                break;
        }
    }

    protected function applyBooleanFilterRelation($item)
    {
        list($field, $condition, $value) = $item;
        $relation = explode('.', $field);
        $fieldName = array_pop($relation);
        $has = implode(".", $relation);

        switch ($value) {
            case true:
                $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                    $query->where($fieldName, "=", 1);
                });
                break;
            case false:
                $this->queryBuilder->whereHas($has, function ($query) use ($fieldName, $value) {
                    $query->where($fieldName, "=", 0);
                });
                break;
            default:
                throw new Exception("Error Processing Request");
                break;
        }
    }
}
