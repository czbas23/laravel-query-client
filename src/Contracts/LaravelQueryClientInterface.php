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
    public function getRetrievingResult();
    public function query(Array $query);
    public function hasQuery(String $commandQuery);
    public function isRetrievingQuery(String $commandQuery);
    public function isConditionalQuery(String $commandQuery);
    public function canRetrieving(String $commandQuery);
    public function setRetrievingQuery(String $commandQuery, $paramQuery);
    public function setConditionalQuery(String $commandQuery, $paramQuery);
    public function validCommandQuery(String $commandQuery);
    public function validParamQuery($paramQuery);
}