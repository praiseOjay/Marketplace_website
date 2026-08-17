<?php

namespace App\EventListener;

use App\Service\RecaptchaService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

#[AsEventListener(event: CheckPassportEvent::class, method: 'onCheckPassport', priority: 100)]
class LoginRecaptchaListener
{
    public function __construct(
        private readonly RecaptchaService $recaptchaService,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return;
        }

        // Only run for POST requests that contain authentication credentials
        if (!$request->isMethod('POST') || !$request->request->has('_password')) {
            return;
        }

        $token = $request->request->get('recaptcha_token') 
            ?? $request->request->get('g-recaptcha-response');

        // Only perform verification if site key is active
        if ($this->recaptchaService->getSiteKey()) {
            $isValid = $this->recaptchaService->verify($token, 'login', $request->getClientIp());
            if (!$isValid) {
                throw new CustomUserMessageAuthenticationException(
                    'Security check failed. Automated bot activity detected. Please try again.'
                );
            }
        }
    }
}
