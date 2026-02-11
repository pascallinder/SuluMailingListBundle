<?php

namespace Linderp\SuluMailingListBundle\Mail\Social;

readonly class MailSocialConfiguration extends InternalMailSocialConfiguration
{

    public function __construct(string $name,
                                string $title,
                                string $src)
    {
        parent::__construct($name,$title,$src,false);
    }
}