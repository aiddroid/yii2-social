<?php

namespace Aiddroid\Social\Interfaces;

/**
 * Interface ProviderInterface.
 */
interface ProviderInterface
{
    /**
     * Redirect to auth page.
     *
     * @param $redirectUrl
     *
     * @return mixed
     */
    public function redirect($redirectUrl);

    /**
     * Get the user's profile.
     *
     * @return mixed
     */
    public function getUser();
}
