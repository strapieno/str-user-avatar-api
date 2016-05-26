<?php
namespace Strapieno\UserAvatar\ApiTest\Asset;

use Matryoshka\Model\Object\ActiveRecord\ActiveRecordInterface;
use Strapieno\UserAvatar\Model\Entity\AvatarAwareInterface;
use Strapieno\UserAvatar\Model\Entity\AvatarAwareTrait;

/**
 * Class Image
 */
class Image implements AvatarAwareInterface, ActiveRecordInterface
{
    use AvatarAwareTrait;

    public function save()
    {
        // TODO: Implement save() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function setId($id)
    {
        // TODO: Implement setId() method.
    }

    public function getId()
    {
        // TODO: Implement getId() method.
    }
}