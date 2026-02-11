<?php

namespace Linderp\SuluMailingListBundle\Mail;

interface MailPoolInterface
{
    /**
     * @return array<int|string, mixed>
     */
    public function getAll(): array;
}
