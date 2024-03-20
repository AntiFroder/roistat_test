<?php

declare(strict_types=1);

/**
 * Так же можно поднять параметр max_allowed_packet на максимум, я думаю это плохая практика. Потому что бд настраивают так
 * что бы все работало нормально не перенагружая сервер
 */
class User
{
    private array $loadedObjects;
    public function __construct(
        private string $field
    )
    {
        $this->field = 'id';
    }

    public function loadUsersByIds(array $ids): array|string
    {
        $idsChunks = $this->getChunks($this->field, $ids);
        if (count($idsChunks) === 0) {
            // В ларе я бы вернул какой-нибудь response с ошибкой или Exception. Все зависит от фронта
            return 'Not found records';
        }
        foreach ($idsChunks as $idsChunk) {
            $idsFilter = $this->_getCondition($idsChunk, $this->field);
            $this->loadedObjects = $this->_loadObjectsByFilter('user', [$idsFilter]);
        }
        return $this->loadedObjects;
    }

    private function getChunks(string $field, array $values): array
    {
        if (count($values) === 0) {
            return [];
        }

        $uniqueValues = array_unique(array_filter($values));
        //кол-во элементов на которые разбивается массив, может варьироваться в зависимости от мощностей бд
        return array_chunk($uniqueValues, 10000);
    }

    private function _quoteString(string $value): string
    {
        $conn = new PDO('');
        return $conn->quote($value);
    }
    private function _loadObjectsByFilter(string $objectName, array $filter = []): array
    {
         //some PDO work, result query will be SELECT * FROM $objectName WHERE ($f 30
         return [];
    }

    private function _getCondition(array $uniqueValues, string $field): string
    {
        $quotedValues = array_map(fn(string $value) => $this->_quoteString($value), $uniqueValues);
        $implodedValues = implode(', ', $quotedValues);

        $quotedField = "`{$field}`";

        return "({$quotedField} IN ({$implodedValues}) AND {$quotedField} IS NOT NULL";
    }

}

