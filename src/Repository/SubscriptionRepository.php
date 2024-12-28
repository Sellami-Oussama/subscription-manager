<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function findActiveSubscriptions(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->setParameter('status', Subscription::STATUS_ACTIVE)
            ->orderBy('s.renewalDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findUpcomingRenewals(int $days = 7): array
    {
        $date = new DateTime();
        $futureDate = new DateTime("+{$days} days");

        return $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->andWhere('s.renewalDate BETWEEN :today AND :future')
            ->setParameter('status', Subscription::STATUS_ACTIVE)
            ->setParameter('today', $date)
            ->setParameter('future', $futureDate)
            ->orderBy('s.renewalDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalMonthlyAmount(): float
    {
        $result = $this->createQueryBuilder('s')
            ->select('SUM(s.price) as total')
            ->where('s.status = :status')
            ->andWhere('s.billingCycle = :cycle')
            ->setParameter('status', Subscription::STATUS_ACTIVE)
            ->setParameter('cycle', Subscription::BILLING_CYCLE_MONTHLY)
            ->getQuery()
            ->getSingleResult();

        return (float)($result['total'] ?? 0);
    }

    public function getTotalYearlyAmount(): float
    {
        $result = $this->createQueryBuilder('s')
            ->select('SUM(s.price) as total')
            ->where('s.status = :status')
            ->andWhere('s.billingCycle = :cycle')
            ->setParameter('status', Subscription::STATUS_ACTIVE)
            ->setParameter('cycle', Subscription::BILLING_CYCLE_YEARLY)
            ->getQuery()
            ->getSingleResult();

        return (float)($result['total'] ?? 0);
    }

    public function getSubscriptionsByCategory(): array
    {
        return $this->createQueryBuilder('s')
            ->select('c.name as category, COUNT(s.id) as count, SUM(s.price) as total')
            ->join('s.category', 'c')
            ->where('s.status = :status')
            ->setParameter('status', Subscription::STATUS_ACTIVE)
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }

    public function getSubscriptionsByProvider(): array
    {
        return $this->createQueryBuilder('s')
            ->select('p.name as provider, COUNT(s.id) as count, SUM(s.price) as total')
            ->join('s.provider', 'p')
            ->where('s.status = :status')
            ->setParameter('status', Subscription::STATUS_ACTIVE)
            ->groupBy('p.id')
            ->getQuery()
            ->getResult();
    }

    public function getTotalQuarterlyAmount(): float
    {
        $result = $this->createQueryBuilder('s')
            ->select('SUM(s.price) as total')
            ->where('s.status = :status')
            ->andWhere('s.billingCycle = :cycle')
            ->setParameter('status', Subscription::STATUS_ACTIVE)
            ->setParameter('cycle', Subscription::BILLING_CYCLE_QUARTERLY)
            ->getQuery()
            ->getSingleResult();

        return (float)($result['total'] ?? 0);
    }

    public function getTotalsByBillingCycle(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.billingCycle, COUNT(s.id) as count, SUM(s.price) as total')
            ->where('s.status = :status')
            ->setParameter('status', Subscription::STATUS_ACTIVE)
            ->groupBy('s.billingCycle')
            ->getQuery()
            ->getResult();
    }

    public function findExpiringSubscriptions(int $days = 30): array
    {
        $date = new DateTime();
        $futureDate = new DateTime("+{$days} days");

        return $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->andWhere('s.renewalDate BETWEEN :today AND :future')
            ->setParameter('status', Subscription::STATUS_ACTIVE)
            ->setParameter('today', $date)
            ->setParameter('future', $futureDate)
            ->orderBy('s.renewalDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalActiveAmount(): float
    {
        $result = $this->createQueryBuilder('s')
            ->select('SUM(s.price) as total')
            ->where('s.status = :status')
            ->setParameter('status', Subscription::STATUS_ACTIVE)
            ->getQuery()
            ->getSingleResult();

        return (float)($result['total'] ?? 0);
    }
}