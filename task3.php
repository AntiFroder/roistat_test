<?php

declare(strict_types=1);

/**
 * {
 *  "id":123456,
 *  "type":"message",
 *  "text":"Hello I'm glad to see you",
 *  "date":"2022-10-01T00:10:00",
 *  "fields":{
 *   "attachment":null
 *  }
 * }
 */

/**
 * 1. Самый очевидный способ избежать дублей это делать запрос к бд на проверку id из тела веб-хука перед сохранением
 * 2. Соединить проверку через кэш и бд(1 пункт)
 *    Добавил бы проверку через кэш: Получаем данные по ключу $this->keyCache -> Если null сохраняем данные и в кэш и в бд
 *      Если не null то пропускаем сохранение и при необходимости отдаем данные на фронт
 *    В этом случае мы избавимся от лишних запросов в бд из 1 пункт
 */
class Webhook
{
    private string $keyCache;
    public function processWebhook(array $data): void
    {
        $type = $data['type'] ?? null;
        if ($type !== 'message') {
            throw new Exception('Unsupported type');
        }
        $message = $data['text'] ?? null;
        $fields = $data['fields'] ?? [];
        if ($message === null) {
            throw new Exception('Text cannot be empty');
        }
        $this->keyCache = $data['id'];

        $this->_storeDeal(
            'Deal from webhook',
            $message,
            json_encode($fields),
        );
    }

    /**
     * сохраняет информацию из веб-хука в базу данных в таблицу deal
     */
    private function _storeDeal(string $title, string $text, array $fields): void
    {
        try {
            $this->_getDataFromCache();
            $this->_getDealByExternalId();

            //some logic to store Deal in database
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }

    }

    private function _setDataToCache(array $data, int $expires): void
    {
        /**
         * Перед сохранением данных в бд создадим запись в кэше с ключом $this->keyCache, примерно на час
         */
        //save data to cache with expire time (in seconds)
    }

    /**
     * загружает уже сохраненную запись из базы данных используя фильтр по полю external_id, конечный запрос в базу данных:
     * SELECT * FROM `deal` WHERE `external_id` = {$this->keyCache};
     */
    private function _getDealByExternalId(): ?array
    {
        // get $dealDB
        if ($dealDB !== null) {
            throw new Exception('double');
        }
        //returns deal from db if its exists, if not - returns null
        return [];
    }

    private function _getDataFromCache(): Exception
    {
        /**
         * Будем получать данные из кэша по ключу $this->keyCache
         */
        $dealCache = Memcache::get($this->keyCache);
        if ($dealCache !== null) {
            throw new Exception('double');
        }
        return $dealCache;
    }


}
