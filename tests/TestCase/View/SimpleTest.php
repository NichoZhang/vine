<?php

namespace Vine\Test\TestCase\View;

class SimpleTest extends \PHPUnit_Framework_TestCase
{

    private $viewRoot = null;

    private $viewSuffix = null;

    private $simpleView = null;

    public function setUp()
    {
        $this->viewRoot = dirname(__FILE__) . "/view/";
        $this->viewSuffix = 'php';
        
        $this->simpleView = new \Vine\Component\View\Simple();
        $this->simpleView->setViewRoot($this->viewRoot);
        $this->simpleView->setViewSuffix($this->viewSuffix);
    }

    public function testViewRoot()
    {
        $this->assertEquals($this->viewRoot, $this->simpleView->getViewRoot());
    }

    public function testViewSuffix()
    {
        $this->assertEquals($this->viewSuffix, $this->simpleView->getViewSuffix());
    }

    public function testRender()
    {
        $this->assertEquals('test render', $this->simpleView->render('simple/render'));
    }

    public function testRenderWithSuffix()
    {
        $this->assertEquals('test render width sufix', $this->simpleView->render('simple/render.suffix', true));
    }

    /**
     * @expectedException \Exception
     */
    public function testRenderNotExistFile()
    {
        $this->simpleView->render('simple/not_exist_file');
    }

    /**
     * @expectedException \Exception
     */
    public function testAssignNameErrorVariable()
    {
        $this->simpleView->assign('0abc', 'value');
    }

    public function testAssignVariable()
    {
        $name = 'string value';
        
        // assign string value
        $this->simpleView->assign('name', $name, false);
        $this->assertEquals($name, $this->simpleView->render('simple/assign_string_value'));
        
        $userList = array(
            array(
                'id' => 1, 
                'name' => 'user1', 
                'age' => 21
            ), 
            array(
                'id' => 2, 
                'name' => 'user2', 
                'age' => 22
            ), 
            array(
                'id' => 3, 
                'name' => 'user3', 
                'age' => 23
            )
        );
        
        // assign array value
        $this->simpleView->assign('userList', $userList, false);
        $this->assertEquals(json_encode($userList), $this->simpleView->render('simple/assign_array_value'));
    }

    public function testAssignVariableWithFilter()
    {
        $name = 'string value<script>alert(1)</script>';
        
        // assign string value with filter
        $this->simpleView->assign('name', $name, true);
        $this->assertEquals(htmlspecialchars($name), $this->simpleView->render('simple/assign_string_value_with_filter'));
        
        // assign array value with filter
        $userList = array(
            array(
                'id' => 1, 
                'name' => 'user1<script>alert(1)</script>', 
                'is_vip' => true, 
                'pet_list' => array()
            ), 
            array(
                'id' => 2, 
                'name' => 'user2<script>alert(2)</script>', 
                'is_vip' => false, 
                'pet_list' => array(
                    array(
                        'name' => '<script>alert("cat")</script>'
                    )
                )
            )
        );
        
        $this->simpleView->assign('userList', $userList, true);
        $this->assertEquals(htmlspecialchars($userList[0]['name'] . $userList[1]['name'] . $userList[1]['pet_list'][0]['name']), $this->simpleView->render('simple/assign_array_value_with_filter'));
    }

    public function testRenderArrayData()
    {
        $data = array(
            'name' => 'render data'
        );
        $this->assertEquals('hello render data', $this->simpleView->render('simple/render_data', false, $data));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testRenderStringData()
    {
        $name = 'render data';
        $this->assertEquals('hello render data', $this->simpleView->render('simple/render_data', false, $name));
    }

    public function testRenderInRender()
    {
        $name = 'string value';
        $this->simpleView->assign('name', $name);
        $this->assertEquals('render in render: ' . $name, $this->simpleView->render('simple/render_in_render'));
    }
}