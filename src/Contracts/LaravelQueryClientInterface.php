<?php
namespace Czbas23\LaravelQueryClient\Contracts;

interface LaravelQueryClientInterface
{
    public function setRelation(Array $relation);
    public function pushRelation(String $relation);
    public function resetRelation();
    public function getRelation();
    public function setCrud(String $crud);
    public function resetCrud();
    public function getCrud();
    public function setModel($model);
    public function getModel();
    public function validCommandQuery(String $commandQuery);
    public function validParamQuery($paramQuery);
    public function hasQuery(String $commandQuery);
    public function query(Array $query);
    public function isRetrievingQuery(String $commandQuery);
    public function canRetrieving(String $commandQuery);
    public function getResult();
    public function setRetrievingQuery(String $commandQuery, $paramQuery=null);
    public function isConditionalQuery(String $commandQuery);
    public function setConditionalQuery(String $commandQuery, $paramQuery=null);
}