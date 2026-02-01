<?php

namespace Linderp\SuluMailingListBundle\Mail;

interface MailPoolInterface
{
    public function getAll(): array;
}