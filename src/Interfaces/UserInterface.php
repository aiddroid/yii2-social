<?php

namespace Aiddroid\Social\Interfaces;

/**
 * Interface UserInterface.
 */
interface UserInterface
{
    /**
     * Get the user's id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get the user nickname.
     *
     * @return mixed
     */
    public function getNickname();

    /**
     * Get avatar url.
     *
     * @return mixed
     */
    public function getAvatar();
}
