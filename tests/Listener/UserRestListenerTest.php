<?php
namespace Strapieno\UserAvatar\ApiTest\Listener;

use ImgMan\Apigility\Entity\ImageEntity;
use Strapieno\NightClubCover\Api\Listener\NightClubRestListener;
use Strapieno\UserAvatar\Api\Listener\UserRestListener;
use Zend\Mvc\Controller\PluginManager;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\ServiceManager\ServiceManager;
use Zend\Uri\Http;
use ZF\Rest\ResourceEvent;

/**
 * Class NightClubRestListenerTest
 */
class UserRestListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $routeConfig = [
        'routes' => [
            'api-rest' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api-rest',
                ],
                'child_routes' => [
                    'user' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/user',
                        ],
                        'child_routes' => [
                            'avatar' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/avatar'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    public function testAttach()
    {
        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');
        $listener = new UserRestListener();
        $this->assertNull($listener->attach($eventManager));
    }

    public function testOnPostUpdate()
    {
        $listener = new UserRestListener();

        $resource = new ResourceEvent();
        $resource->setParam('id', 'test');
        $imageService = new  ImageEntity();
        $resource->setParam('image', $imageService);

        /** @var $route TreeRouteStack */
        $route = TreeRouteStack::factory($this->routeConfig);
        $route->setRequestUri(new Http('www.test.com'));

        $sm = new ServiceManager();
        $sm->setService('Router', $route);

        $abstractLocator = new PluginManager();
        $abstractLocator->setServiceLocator($sm);


        $image = $this->getMockBuilder('Strapieno\UserAvatar\ApiTest\Asset\Image')
            ->getMock();

        $resultSet = $this->getMockBuilder('Matryoshka\Model\ResultSet\HydratingResultSet')
            ->setMethods(['current'])
            ->getMock();

        $resultSet->method('current')
            ->willReturn($image);

        $model = $this->getMockBuilder('Strapieno\User\Model\UserModelService')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $model->method('find')
            ->willReturn($resultSet);

        $listener->setUserModelService($model);
        $listener->setServiceLocator($abstractLocator);
        $this->assertSame($listener->onPostUpdate($resource), $imageService);

        $imageService->setSrc('test');
        $this->assertSame($listener->onPostUpdate($resource), $imageService);
    }

    public function testOnDeleteUpdate()
    {
        $listener = new UserRestListener();

        $resource = new ResourceEvent();
        $resource->setParam('id', 'test');
        $imageService = new  ImageEntity();


        $sm = new ServiceManager();
        $abstractLocator = new PluginManager();
        $abstractLocator->setServiceLocator($sm);


        $image = $this->getMockBuilder('Strapieno\UserAvatar\ApiTest\Asset\Image')
            ->getMock();

        $resultSet = $this->getMockBuilder('Matryoshka\Model\ResultSet\HydratingResultSet')
            ->setMethods(['current'])
            ->getMock();

        $resultSet->method('current')
            ->willReturn($image);

        $model = $this->getMockBuilder('Strapieno\User\Model\UserModelService')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $model->method('find')
            ->willReturn($resultSet);

        $listener->setUserModelService($model);
        $listener->setServiceLocator($abstractLocator);
        $this->assertTrue($listener->onPostDelete($resource));
    }
}