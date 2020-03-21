<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\Tests\JsonMapper\Implementation;

use DannyVanDerSluijs\Tests\JsonMapper\Implementation\Models\User;

class ComplexObject
{
    /** @var SimpleObject */
    private $child;
    /** @var User */
    private $user;

    public function getChild(): SimpleObject
    {
        return $this->child;
    }

    public function setChild(SimpleObject $child): void
    {
        $this->child = $child;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
