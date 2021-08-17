<?php

namespace Emsifa\Evo\Rules\Concerns;

use Illuminate\Validation\DatabasePresenceVerifierInterface;
use Illuminate\Validation\PresenceVerifierInterface;
use RuntimeException;

trait PresenceVerifier
{
    protected ?PresenceVerifierInterface $presenceVerifier = null;

    public function setPresenceVerifier(PresenceVerifierInterface $verifier)
    {
        $this->presenceVerifier = $verifier;
    }

    public function getPresenceVerifier($connection = null)
    {
        if (is_null($this->presenceVerifier)) {
            throw new RuntimeException('Presence verifier has not been set.');
        }

        $presenceVerifier = $this->presenceVerifier;

        if ($presenceVerifier instanceof DatabasePresenceVerifierInterface) {
            $presenceVerifier->setConnection($connection);
        }

        return $presenceVerifier;
    }
}
