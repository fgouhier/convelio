<?php

use Faker\Factory;

class SiteRepository implements Repository
{
    use SingletonTrait;

    /**
     * @param int $id
     *
     * @return Site
     */
    public function getById($id)
    {
        // DO NOT MODIFY THIS METHOD
        $generator = Factory::create();
        $generator->seed($id);

        return new Site($id, $generator->url);
    }
}
