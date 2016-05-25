<?php
namespace Strapieno\UserAvatar\Api\Listener;

use ImgMan\Apigility\Entity\ImageEntityInterface;
use ImgMan\Image\SrcAwareInterface;
use Matryoshka\Model\Object\ActiveRecord\ActiveRecordInterface;
use Matryoshka\Model\Object\IdentityAwareInterface;
use Matryoshka\Model\Wrapper\Mongo\Criteria\ActiveRecord\ActiveRecordCriteria;
use Strapieno\User\Model\Entity\UserInterface;
use Strapieno\User\Model\UserModelAwareInterface;
use Strapieno\User\Model\UserModelAwareTrait;
use Strapieno\UserAvatar\Model\Entity\UserAvatarAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use ZF\Rest\ResourceEvent;

/**
 * Class UserRestListener
 */
class UserRestListener implements ListenerAggregateInterface,
    ServiceLocatorAwareInterface,
    UserModelAwareInterface
{
    use ListenerAggregateTrait;
    use ServiceLocatorAwareTrait;
    use UserModelAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('update', [$this, 'onPostUpdate']);
        $this->listeners[] = $events->attach('delete', [$this, 'onPostDelete']);
    }

    /**
     * @param ResourceEvent $e
     * @return mixed
     */
    public function onPostUpdate(ResourceEvent $e)
    {
        $serviceLocator = $this->getServiceLocator();
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        $id  = $e->getParam('id');
        $user = $this->getUserFromId($id);
        $image = $e->getParam('image');
        if ($user instanceof UserAvatarAwareInterface && $user instanceof ActiveRecordInterface) {

            $user->setAvatar($this->getUrlFromImage($image, $serviceLocator));
            $user->save();
        }
        return $image;
    }

    /**
     * @param ResourceEvent $e
     * @return bool
     */
    public function onPostDelete(ResourceEvent $e)
    {

        $id  = $e->getParam('id');
        $user = $this->getUserFromId($id);

        if ($user instanceof UserAvatarAwareInterface && $user instanceof ActiveRecordInterface) {

            $user->setAvatar(null);
            $user->save();
        }
        return true;
    }

    /**
     * @param $id
     * @return UserInterface|null
     */
    protected function getUserFromId($id)
    {
        return $this->getUserModelService()->find((new ActiveRecordCriteria())->setId($id))->current();

    }

    /**
     * @param IdentityAwareInterface $image
     * @param $serviceLocator
     * @return string
     */
    protected function getUrlFromImage(IdentityAwareInterface $image, $serviceLocator)
    {
        $now = new \DateTime();
        if ($image instanceof SrcAwareInterface && $image->getSrc()) {

            return $image->getSrc(). '?lastUpdate=' . $now->getTimestamp();
        }

        $router = $serviceLocator->get('Router');
        $url = $router->assemble(
            ['user_id' => $image->getId()],
            ['name' => 'api-rest/user/avatar', 'force_canonical' => true]
        );

        return $url . '?lastUpdate=' . $now->getTimestamp();
    }
}