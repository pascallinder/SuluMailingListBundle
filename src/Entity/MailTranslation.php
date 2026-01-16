<?php

namespace Linderp\SuluMailingListBundle\Entity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Linderp\SuluBaseBundle\Entity\IdTrait;
use Sulu\Bundle\MediaBundle\Entity\Media;

class MailTranslation
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $subject = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $title = null;

    #[ORM\Column(type: Types::STRING, length: 8192, nullable: true)]
    protected ?string $body = null;
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'header_image_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    protected ?Media $headerImage = null;

    /**
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     */
    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
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
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string|null $body
     */
    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return Media|null
     */
    public function getHeaderImage(): ?Media
    {
        return $this->headerImage;
    }

    /**
     * @param Media|null $headerImage
     */
    public function setHeaderImage(?Media $headerImage): void
    {
        $this->headerImage = $headerImage;
    }
}