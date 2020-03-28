<?php

namespace App\Service;

use App\Service\Statistic\StatisticEvent;

class StatisticService
{
    /**
     * @var RedisService
     */
    private RedisService $redis;

    private const STATISTIC_KEY = 'stat';

    private const STATISTIC_INFO_KEY = 'statInfo';

    public function __construct(RedisService $redis)
    {
        $this->redis = $redis;
    }

    /**
     * вызывается после terminate
     * @param StatisticEvent $event
     */
    public function appendData(StatisticEvent $event)
    {
        $this->redis->getConnection()->incr($this->getStatisticName($event->getCountry()));
    }

    /**
     * @param string $country
     * @return bool|mixed|string
     */
    public function getInfo(string $country): int
    {
        return (int)$this->redis->getConnection()->get($country);
    }

    /**
     * todo::неплохо наверно тут ещё упаковывать данные для большей читабельности
     * @return array
     */
    public function getAllInfo(): array
    {
        /**
         * пытаемся получить готовую статистику
         */
        $info = $this->redis->getConnection()->get(self::STATISTIC_INFO_KEY);
        if (is_string($info)) {
            return unserialize($info);
        }

        /**
         * если статистики нету формируем её заного
         * получаем все страны по шаблону
         */
        $keys = $this->redis->getConnection()->keys($this->getStatisticName('*'));
        $info = [];
        foreach ($keys as $key) {
            try {
                $info[$this->getCountryByKey($key)] = (int)$this->redis->getConnection()->get($key);
            } catch (\Throwable $e) {

            }
        }

        /**
         * сохраняем её на 5 секунд и отдаем
         */
        if (count($info) !== 0) {
            $this->redis->getConnection()->set(self::STATISTIC_INFO_KEY, serialize($info), 5000);
        }

        return $info;
    }

    /**
     * @param string $country
     * @return string
     */
    private function getStatisticName(string $country): string
    {
        return self::STATISTIC_KEY . ':' . $country;
    }

    /**
     * @param string $key
     * @return mixed|string
     */
    private function getCountryByKey(string $key)
    {
        return explode(':', $key)[1];
    }
}