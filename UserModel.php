<?php

namespace shenoda\phpmvc;

use shenoda\phpmvc\db\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}