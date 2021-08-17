<?php

namespace Emsifa\Evo\Contracts;

use Illuminate\Validation\PresenceVerifierInterface;

interface HasPresenceVerifier
{
    public function setPresenceVerifier(PresenceVerifierInterface $verifier);

    public function getPresenceVerifier($connection = null);
}
