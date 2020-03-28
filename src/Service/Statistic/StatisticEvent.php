<?php

namespace App\Service\Statistic;

use Symfony\Contracts\EventDispatcher\Event;

class StatisticEvent extends Event
{
    /**
     * @var string
     */
    private string $country;

    /**
     * StatisticEvent constructor.
     * @param string $country
     */
    public function __construct(string $country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }
}