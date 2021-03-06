<?php

namespace Charcoal\Tests\App\Route;

use \Charcoal\App\Route\RouteConfig;

class RouteConfigTest extends \PHPUnit_Framework_TestCase
{
    public $obj;

    public function setUp()
    {
        $this->obj = new RouteConfig();
    }

    public function testSetIdent()
    {
        $this->assertNull($this->obj->ident());
        $ret = $this->obj->setIdent('foobar');
        $this->assertSame($ret, $this->obj);

        $this->assertEquals('foobar', $this->obj->ident());

        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->setIdent(false);
    }

    public function testSetRoute()
    {
        $this->assertNull($this->obj->route());
        $ret = $this->obj->setRoute('foobar');
        $this->assertSame($ret, $this->obj);

        $this->assertEquals('foobar', $this->obj->route());

        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->setRoute(false);
    }

    public function testSetGroups()
    {
        $this->assertEquals([], $this->obj->groups());
        $ret = $this->obj->setGroups(['foo', 'bar']);
        $this->assertSame($ret, $this->obj);

        $this->assertEquals(['foo', 'bar'], $this->obj->groups());
    }

    public function testAddGroup()
    {
        $this->obj->addGroup('foo');
        $this->obj->addGroup('bar');

        $this->assertEquals(['foo', 'bar'], $this->obj->groups());

        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->addGroup(false);
    }

    public function testSetController()
    {
        $this->assertNull($this->obj->controller());
        $ret = $this->obj->setController('foobar');
        $this->assertSame($ret, $this->obj);

        $this->assertEquals('foobar', $this->obj->controller());

        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->setController(false);
    }

    public function testSetMethods()
    {
        $this->assertEquals(['GET'], $this->obj->methods());
        $ret = $this->obj->setMethods(['POST']);
        $this->assertSame($ret, $this->obj);

        $this->assertEquals(['POST'], $this->obj->methods());
    }

    public function testAddMethod()
    {
        $this->assertEquals(['GET'], $this->obj->methods());
        $ret = $this->obj->addMethod('post');
        $this->assertSame($ret, $this->obj);

        $this->assertEquals(['GET', 'POST'], $this->obj->methods());

        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->addMethod([]);
    }

    public function testAddMethodInvalidMethodThrowsException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->addMethod('invalid');
    }
}
