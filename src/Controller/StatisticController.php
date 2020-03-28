<?php

namespace App\Controller;

use App\Service\Statistic\StatisticEvent;
use App\Service\StatisticService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;

class StatisticController extends AbstractController
{

    /**
     * @var EventDispatcher
     */
    private EventDispatcher $eventDispatcher;
    /**
     * @var StatisticService
     */
    private StatisticService $statisticService;

    public function __construct(EventDispatcher $eventDispatcher, StatisticService $statisticService)
    {
        $this->eventDispatcher  = $eventDispatcher;
        $this->statisticService = $statisticService;
    }

    /**
     * @Route("/statistic", name="statistic", methods={"GET"})
     * @return JsonResponse
     */
    public function allInfo(): JsonResponse
    {
        $response = $this->statisticService->getAllInfo();

        return $this->json(serialize($response));

    }

    /**
     * @Route("/statistic/{country}", name="statistic", methods={"GET"})
     * @param string $country
     * @return JsonResponse
     */
    public function info(string $country): JsonResponse
    {
        $info = $this->statisticService->getInfo($country);

        return $this->json(sprintf('{"%s":"%d"}', $country, $info));
    }

    /**
     * @Route("/statistic/{country}", name="statistic", methods={"POST"})
     * @param string $country
     * @return JsonResponse
     */
    public function appendInfo(string $country): JsonResponse
    {
        $this->eventDispatcher->dispatch(new StatisticEvent($country), 'statistic_append');

        return $this->json('{"success":true}');
    }
}
