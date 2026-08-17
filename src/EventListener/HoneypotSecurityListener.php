<?php

namespace App\EventListener;

use App\Service\HoneypotValidator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

#[AsEventListener(event: CheckPassportEvent::class, method: 'onCheckPassport', priority: 100)]
class HoneypotSecurityListener
{
    public function __construct(
        private readonly HoneypotValidator $honeypotValidator,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return;
        }

        if ($this->honeypotValidator->isSpam($request)) {
            throw new CustomUserMessageAuthenticationException('Invalid submission detected.');
        }
    }
}
