<?php

namespace Czbas23\LaravelQueryClient;

use Czbas23\LaravelQueryClient\Contracts\LaravelQueryClientInterface;

class LaravelQueryClient implements LaravelQueryClientInterface
{
    protected $retrievingQueryCreate = ['insert', 'insertGetId', 'create', 'fill'];

    protected $retrievingQueryRead = ['all', 'get', 'first', 'firstOrFail', 'find', 'findOrFail', 'value', 'pluck', 'count', 'max', 'avg', 'exists', 'doesntExist'];

    protected $retrievingQueryUpdate = ['update', 'increment', 'decrement', 'updateOrCreate'];

    protected $retrievingQueryDelete = ['delete', 'truncate', 'destroy'];

    protected $conditionalQuerySelect = ['select', 'distinct', 'addSelect'];

    protected $conditionalQueryJoin = ['join', 'joinWhere', 'joinSub', 'leftJoin', 'leftJoinWhere', 'leftJoinSub', 'rightJoin', 'rightJoinWhere', 'rightJoinSub', 'crossJoin', 'newJoinClause'];

    protected $conditionalQueryWhere = ['where', 'orWhere', 'whereBetween', 'whereNotBetween', 'whereIn', 'whereNotIn', 'whereNull', 'whereNotNull', 'whereDate', 'whereMonth', 'whereDay', 'whereYear', 'whereTime', 'whereColumn', 'whereJsonContains', 'whereJsonLength', 'withTrashed', 'onlyTrashed'];

    protected $conditionalQueryOrder = ['orderBy', 'latest', 'oldest', 'inRandomOrder'];

    protected $conditionalQueryGroup = ['groupBy', 'having'];

    protected $conditionalQueryLimit = ['skip', 'take', 'offset', 'limit'];

    protected $conditionalQueryRelation = ['with', 'has', 'doesntHave', 'withCount', 'whereHas'];

    protected $propertyRetrievingQueryList = ['retrievingQueryCreate', 'retrievingQueryRead', 'retrievingQueryUpdate', 'retrievingQueryDelete'];

    protected $propertyConditionalQueryList = ['conditionalQuerySelect', 'conditionalQueryJoin', 'conditionalQueryWhere', 'conditionalQueryOrder', 'conditionalQueryGroup', 'conditionalQueryLimit', 'conditionalQueryRelation'];

    protected $propertyQueryList = ['propertyRetrievingQueryList', 'propertyConditionalQueryList'];

    protected $crud;

    protected $model;

    protected $group = [];

    protected $retrievingResult;

    protected $relation = [];

    public function __construct() {
        //
    }

    public function setRelation(Array $relation)
    {
        $this->relation = $relation;
        return $this;
    }

    public function pushRelation(String $relation)
    {
        $this->relation[] = $relation;
        return $this;
    }

    public function resetRelation()
    {
        $this->relation = [];
        return $this;
    }

    public function getRelation()
    {
        return $this->relation;
    }

    public function setCrud(String $crud)
    {
        $crud = strtolower($crud);
        if(in_array($crud, ['create', 'read', 'update', 'delete'])){
            $this->crud = $crud;
        } else {
            throw new \Exception('Parameter must in create, read, update, delete.');
        }
        return $this;
    }

    public function resetCrud()
    {
        $this->crud = null;
        return $this;
    }

    public function getCrud()
    {
        return $this->crud;
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function validCommandQuery(String $commandQuery)
    {
        if(!$this->hasQuery($commandQuery)){
            throw new \Exception('Command not valid.');
        }
        return true;
    }

    public function validParamQuery($paramQuery)
    {
        if(is_null($paramQuery) || is_array($paramQuery)){
            return true;
        }
        throw new \Exception('Parameter not valid.');
    }

    public function hasQuery(String $commandQuery)
    {
        foreach ($this->propertyQueryList as $propertyTypeQuery) {
            foreach ($this->$propertyTypeQuery as $propertySubTypeQuery) {
                foreach ($this->$propertySubTypeQuery as $query) {
                    if($commandQuery === $query){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function query(Array $query)
    {
        if(!$this->model){
            throw new \Exception('Propety model not set.');
        }
        foreach ($query as $query_row) {
            $commandQuery = $query_row[0] ?? null;
            $paramQuery = $query_row[1] ?? null;
            $this->validCommandQuery($commandQuery);
            $this->validParamQuery($paramQuery);
            if($this->isRetrievingQuery($commandQuery)){
                $this->setRetrievingQuery($commandQuery, $paramQuery);
            } else {
                $this->setConditionalQuery($commandQuery, $paramQuery);
            }
        }
        return $this;
    }

    public function isRetrievingQuery(String $commandQuery)
    {
        foreach ($this->propertyRetrievingQueryList as $propertySubTypeQuery) {
            foreach ($this->$propertySubTypeQuery as $query) {
                if($commandQuery === $query){
                    return true;
                }
            }
        }
        return false;
    }

    public function canRetrieving(String $commandQuery)
    {
        if(isset($this->crud) && in_array($commandQuery, $this->{'retrievingQuery'.ucfirst($this->crud)})){
            return true;
        }
        throw new \Exception('Method not allow with crud.');
    }

    public function getResult()
    {
        return $this->retrievingResult;
    }

    public function setRetrievingQuery(String $commandQuery, $paramQuery=null)
    {
        if($this->canRetrieving($commandQuery)){
            if(is_null($paramQuery)){
                $this->retrievingResult = $this->model->$commandQuery();
            } else {
                $this->retrievingResult = call_user_func_array(array($this->model, $commandQuery), $paramQuery);
            }
        }
    }

    public function isConditionalQuery(String $commandQuery)
    {
        foreach ($this->propertyConditionalQueryList as $propertySubTypeQuery) {
            foreach ($this->$propertySubTypeQuery as $query) {
                if($commandQuery === $query){
                    return true;
                }
            }
        }
        return false;
    }

    public function setConditionalQuery(String $commandQuery, $paramQuery=null)
    {
        if(in_array($commandQuery, $this->conditionalQueryRelation)){
            if(is_null($paramQuery)){
                throw new \Exception('Relation parameter not null.');
            }
            if(is_array($paramQuery[0])){
                foreach ($paramQuery[0] as $paramQuery_v) {
                    if(!in_array($paramQuery_v, $this->relation)){
                        throw new \Exception('Relation not match.');
                    }
                }
            } else if (is_string($paramQuery[0])) {
                if(!in_array($paramQuery[0], $this->relation)){
                    throw new \Exception('Relation not match.');
                }
            }
            if($commandQuery === 'whereHas'){
                if(!is_string($paramQuery[0])){
                    throw new \Exception('Command whereHas argument 0 must string.');
                }
                if(!is_array($paramQuery[1])){
                    throw new \Exception('Command whereHas argument 1 must array.');
                }
                $this->model = $this->model->whereHas($paramQuery[0], function ($model) use ($paramQuery) {
                    $this->model = $model;
                    $this->query($paramQuery[1]);
                });
            } else {
                $this->model = call_user_func_array(array($this->model, $commandQuery), $paramQuery);
            }
        } else {
            if(is_null($paramQuery)){
                $this->model->$commandQuery();
            } else {
                if(in_array($commandQuery, ['where', 'orWhere']) && $this->hasQuery($paramQuery[0][0])){
                    $this->model = $this->model->$commandQuery(function ($model) use ($paramQuery) {
                        $this->model = $model;
                        $this->query($paramQuery);
                    });
                } else {
                    $this->model = call_user_func_array(array($this->model, $commandQuery), $paramQuery);
                }
            }
        }
    }

}
