<?php

namespace App\Doctrine;

class Cache implements \Doctrine\Common\Cache\Cache
{

    /**
     * @inheritDoc
     * @param string $id
     * @return mixed
     */
    public function fetch($id): mixed
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function contains($id): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function save($id, $data, $lifeTime = 0): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete($id): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getStats()
    {
        // TODO: Implement getStats() method.
    }
}
