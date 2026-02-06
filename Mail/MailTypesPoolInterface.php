<?php

namespace Linderp\SuluMailingListBundle\Mail;

interface MailTypesPoolInterface extends MailPoolInterface
{
    public function getAllSorted(): array;
}