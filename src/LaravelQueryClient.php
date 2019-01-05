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

    protected $conditionalQueryRelation = ['with', 'has', 'doesntHave', 'withCount'];

    protected $conditionalQueryOptional = ['whereGroup'];

    protected $propertyRetrievingQueryList = ['retrievingQueryCreate', 'retrievingQueryRead', 'retrievingQueryUpdate', 'retrievingQueryDelete'];

    protected $propertyConditionalQueryList = ['conditionalQuerySelect', 'conditionalQueryJoin', 'conditionalQueryWhere', 'conditionalQueryOrder', 'conditionalQueryGroup', 'conditionalQueryLimit', 'conditionalQueryRelation', 'conditionalQueryOptional'];

    protected $propertyQueryList = ['propertyRetrievingQueryList', 'propertyConditionalQueryList'];

    protected $crud;

    protected $model;

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

    public function getRetrievingResult()
    {
        return $this->retrievingResult;
    }

    public function query(Array $query)
    {
        if(!$this->model){
            throw new \Exception('Propety model not set.');
        }
        foreach ($query as $commandQuery => $paramQuery) {
            if($this->hasQuery($commandQuery)){
                if($this->isRetrievingQuery($commandQuery)){
                    if($this->canRetrieving($commandQuery)){
                        $this->setRetrievingQuery($commandQuery, $paramQuery);
                    }
                } else {
                    $this->setConditionalQuery($commandQuery, $paramQuery);
                }
            }
        }
        return $this;
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

    public function canRetrieving(String $commandQuery)
    {
        if(isset($this->crud) && in_array($commandQuery, $this->{'retrievingQuery'.ucfirst($this->crud)})){
            return true;
        }
        throw new \Exception('Method not allow with crud.');
    }

    public function setRetrievingQuery(String $commandQuery, $paramQuery)
    {
        if(is_array($paramQuery)){
            $this->retrievingResult = call_user_func_array(array($this->model, $commandQuery), $paramQuery);
        } else {
            $this->retrievingResult = call_user_func(array($this->model, $commandQuery), $paramQuery);
        }
    }

    public function setConditionalQuery(String $commandQuery, $paramQuery)
    {
        if(in_array($commandQuery, $this->conditionalQueryRelation)){
            if(is_array($paramQuery)){
                foreach ($paramQuery[0] as $paramQuery_v) {
                    if(!in_array($paramQuery_v, $this->relation)){
                        throw new \Exception('Relation not match.');
                    }
                }
                $this->model = call_user_func_array(array($this->model, $commandQuery), [$paramQuery[0]]);
            } else if (is_string($paramQuery)) {
                if(!in_array($paramQuery, $this->relation)){
                    throw new \Exception('Relation not match.');
                }
                $this->model = call_user_func(array($this->model, $commandQuery), $paramQuery);
            }
        } else {
            if(is_array($paramQuery)){
                $this->model = call_user_func_array(array($this->model, $commandQuery), $paramQuery);
            } else {
                $this->model = call_user_func(array($this->model, $commandQuery), $paramQuery);
            }
        }
    }
}
