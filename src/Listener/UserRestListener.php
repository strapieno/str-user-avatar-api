<?php
namespace Strapieno\UserAvatar\Api\Listener;

use Matryoshka\Model\Object\ActiveRecord\ActiveRecordInterface;
use Matryoshka\Model\Wrapper\Mongo\Criteria\ActiveRecord\ActiveRecordCriteria;
use Strapieno\User\Model\Entity\UserInterface;
use Strapieno\User\Model\UserModelAwareInterface;
use Strapieno\User\Model\UserModelAwareTrait;
use Strapieno\UserAvatar\Model\Entity\UserAvatarAwareInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

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
        $this->listeners[] = $events->attach('update.post', [$this, 'onPostUpdate']);
        $this->listeners[] = $events->attach('delete.post', [$this, 'onPostDelete']);
    }

    /**
     * @param Event $e
     */
    public function onPostUpdate(Event $e)
    {
        $serviceLocator = $this->getServiceLocator();
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        $id  = $e->getParam('id');
        $user = $this->getUserFromId($id);

        if ($user instanceof UserAvatarAwareInterface && $user instanceof ActiveRecordInterface) {

            /** @var $router RouteInterface */
            $router = $serviceLocator->get('Router');
            $url = $router->assemble(
                ['user_id' => $id],
                ['name' => 'api-rest/user/avatar', 'force_canonical' => true]
            );

            $now = new \DateTime();
            $user->setAvatar($url . '?lastUpdate=' . $now->getTimestamp());
            $user->save();
        }
    }

    /**
     * @param Event $e
     */
    public function onPostDelete(Event $e)
    {

        $id  = $e->getParam('id');
        $user = $this->getUserFromId($id);

        if ($user instanceof UserAvatarAwareInterface && $user instanceof ActiveRecordInterface) {

            $user->setAvatar(null);
            $user->save();
        }
    }

    /**
     * @param $id
     * @return UserInterface|null
     */
    protected function getUserFromId($id)
    {
        return $this->getUserModelService()->find((new ActiveRecordCriteria())->setId($id))->current();

    }
}