<?php

namespace Linderp\SuluMailingListBundle\Entity\Newsletter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Linderp\SuluBaseBundle\Entity\IdTrait;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterTranslationRepository;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity(repositoryClass: NewsletterTranslationRepository::class)]
class NewsletterTranslation implements AuditableInterface
{
    use IdTrait;
    use AuditableTrait;
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $title = null;
    public function __construct(
        #[ORM\ManyToOne(targetEntity: Newsletter::class, inversedBy: 'translations')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private readonly Newsletter $newsletter,
        #[ORM\Column(type: Types::STRING, length: 5, nullable: false)]
        private readonly string $locale,
    ) {
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return Newsletter
     */
    public function getNewsletter(): Newsletter
    {
        return $this->newsletter;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

}