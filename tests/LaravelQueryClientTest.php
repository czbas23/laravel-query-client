<?php

namespace Czbas23\LaravelQueryClient\Tests;

use PHPUnit\Framework\TestCase;

use Czbas23\LaravelQueryClient\LaravelQueryClient;
use Illuminate\Database\Eloquent\Model;

class LaravelQueryClientTest extends TestCase
{
    protected $laravelQueryClient;
    public function setUp()
    {
        $this->laravelQueryClient = new LaravelQueryClient;
    }

    /**
     * test set relation.
     *
     * @return void
     */
    public function testSetRelationTest()
    {
        $this->assertEquals([], $this->laravelQueryClient->getRelation());
        $this->laravelQueryClient->setRelation(['comments']);
        $this->assertEquals(['comments'], $this->laravelQueryClient->getRelation());
        $this->laravelQueryClient->pushRelation('tags');
        $this->assertEquals(['comments', 'tags'], $this->laravelQueryClient->getRelation());
        $this->laravelQueryClient->resetRelation();
        $this->assertEquals([], $this->laravelQueryClient->getRelation());
    }

    /**
     * test set crud.
     *
     * @return void
     */
    public function testSetCrudTest()
    {
        $this->assertEquals(null, $this->laravelQueryClient->getCrud());
        $this->laravelQueryClient->setCrud('Read');
        $this->assertEquals('read', $this->laravelQueryClient->getCrud());
        $this->laravelQueryClient->setCrud('create');
        $this->assertEquals('create', $this->laravelQueryClient->getCrud());
        try {
            $this->laravelQueryClient->setCrud('other');
        } catch (\Exception $e){
            $this->assertEquals('Parameter must in create, read, update, delete.', $e->getMessage());
            $this->assertEquals('create', $this->laravelQueryClient->getCrud());
        }
        $this->laravelQueryClient->resetCrud();
        $this->assertEquals(null, $this->laravelQueryClient->getCrud());
    }

    /**
     * test hasQuery method.
     *
     * @return void
     */
    public function testHasQueryTest()
    {
        $this->assertEquals(true, $this->laravelQueryClient->hasQuery('insert'));
        $this->assertEquals(false, $this->laravelQueryClient->hasQuery('other'));
    }

    /**
     * test isRetrievingQuery method.
     *
     * @return void
     */
    public function testIsRetrievingQueryTest()
    {
        $this->assertEquals(true, $this->laravelQueryClient->isRetrievingQuery('insert'));
        $this->assertEquals(false, $this->laravelQueryClient->isRetrievingQuery('where'));
    }

    
    /**
     * test isConditionalQuery method.
     *
     * @return void
     */
    public function testIsConditionalQueryTest()
    {
        $this->assertEquals(true, $this->laravelQueryClient->isConditionalQuery('where'));
        $this->assertEquals(false, $this->laravelQueryClient->isConditionalQuery('insert'));
    }

    /**
     * test canRetrieving method.
     *
     * @return void
     */
    public function testCanRetrievingTest()
    {
        $this->laravelQueryClient->setCrud('read');
        $this->assertEquals(true, $this->laravelQueryClient->canRetrieving('find'));
        try {
            $this->laravelQueryClient->canRetrieving('insert');
        } catch (\Exception $e) {
            $this->assertEquals('Method not allow with crud.', $e->getMessage());
        }
    }

    /**
     * test setConditionalQuery method.
     *
     * @return void
     */
    public function testSetConditionalQueryTest()
    {
        $mock = $this->getMockBuilder(stdClass::class)
        ->setMethods(['with'])
        ->getMock();
        $mock->method('with')
        ->willReturn('foo');
        $this->laravelQueryClient->setModel($mock)->setRelation(['comments']);
        $this->laravelQueryClient->setConditionalQuery('with', 'comments');
        $this->assertEquals('foo', $this->laravelQueryClient->getModel());
        try {
            $this->laravelQueryClient->setConditionalQuery('with', 'tags');
        } catch (\Exception $e) {
            $this->assertEquals('Relation not match.', $e->getMessage());
        }
    }

}
