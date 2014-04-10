<?php

namespace CanalTP\SamEcoreApplicationManagerBundle\Security;

interface BusinessObjectTypeInterface extends CommonBusinessInterface
{
    public function getSpecificPermissions();
    public function hasPermissionClassLevel();
    public function hasPermissionInstanceLevel();
    public function getChilds();
    public function getClassName();
    public function getInstances();
}
