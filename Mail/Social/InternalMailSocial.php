<?php

namespace Linderp\SuluMailingListBundle\Mail\Social;

final class InternalMailSocial
{
    public function __construct(private InternalMailSocialConfiguration $mailSocialConfiguration){}
    public function getConfiguration(): InternalMailSocialConfiguration{
        return $this->mailSocialConfiguration;
    }
}