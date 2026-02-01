<?php

namespace Linderp\SuluMailingListBundle\Controller\Admin;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Linderp\SuluBaseBundle\Common\DoctrineListRepresentationFactory;
use Linderp\SuluBaseBundle\Controller\Admin\LocaleController;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @phpstan-type NewsletterData array{
 *     id: int|null,
 *     enabled: bool,
 *     title: string,
 *     category: array{id: int}|null,
 * }
 * @extends LocaleController<Newsletter>
 */
class FilteredContactsController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $em){}
    #[Route(path: '/admin/api/filtered-contacts', name: 'app.get_filtered_contacts', methods: ['GET'])]
    public function getAction(Request $request): JsonResponse
    {
        $page  = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, (int) $request->query->get('limit', 10));
        $offset = ($page - 1) * $limit;

        $newsletterIds = $request->query->get('newsletterIds') ?explode(',', $request->query->get('newsletterIds')):[];
        $contactIds = $request->query->get('ids') ? explode(',', $request->query->get('ids')) : [];
        if (!$newsletterIds) {
            return $this->json(new PaginatedRepresentation([], 'filtered_contacts', $page, $limit, 0));
        }
        $qb = $this->em->createQueryBuilder()
            ->select('c.id AS id, c.firstName AS firstName, c.lastName AS lastName, c.mainEmail AS mainEmail')
            ->from(NewsletterSubscription::class, 's')
            ->innerJoin('s.contact', 'c')
            ->innerJoin('s.newsletter', 'n')
            ->andWhere('n.id IN (:newsletterIds)')
            ->andWhere('s.isConfirmed = true')
            ->andWhere('s.isUnsubscribed = false')
            ->setParameter('newsletterIds', $newsletterIds)
            ->groupBy('c.id, c.firstName, c.lastName, c.mainEmail')
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        if(count($contactIds)){
            $qb->andWhere('c.id IN (:contactIds)')
                ->setParameter('contactIds', $contactIds);
        }
        // Optional search support (selection overlay sends "search")
        $search = trim((string) $request->query->get('search', ''));
        if ($search !== '') {
            $qb->andWhere('c.firstName LIKE :q OR c.lastName LIKE :q OR c.mainEmail LIKE :q')
                ->setParameter('q', '%' . $search . '%');
        }

        $items = $qb->getQuery()->getArrayResult();
        $paginator = new Paginator($qb);

        return $this->json((new PaginatedRepresentation($items, 'filtered_contacts', $page, $limit, $paginator->count()))->toArray());
    }

}