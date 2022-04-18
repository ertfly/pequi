<?php

namespace Pequi;

abstract class MongoEntity implements \MongoDB\BSON\Unserializable
{
    abstract public function setId($id);
    abstract public function getId();
    abstract public function toArray();
    public function insert($db)
    {
        $collection = static::TABLE;
        if (!$this->getId()) {
            $counter = $db->counter->findOne(['_id' => $collection]);
            $seq = 1;
            if (is_null($counter)) {
                $db->counter->insertOne([
                    '_id' => $collection,
                    'seq' => $seq,
                ]);
            } else {
                $seq = $counter->seq + 1;
                $db->counter->updateOne([
                    '_id' => $collection,
                ], [
                    '$set' => [
                        'seq' => $seq
                    ],
                ]);
            }
            $this->setId($seq);
        }
        $db->$collection->insertOne($this->toArray());
        return;
    }
    public function update($db)
    {
        $collection = static::TABLE;
        $db->$collection->updateOne([
            '_id' => $this->getId(),
        ], [
            '$set' => $this->toArray(),
        ]);
    }
    public function delete($db)
    {
        $collection = static::TABLE;
        $db->$collection->updateOne([
            '_id' => $this->getId(),
        ], [
            '$set' => ['trash' => true],
        ]);
    }
    public function destroy($db)
    {
        $collection = static::TABLE;
        $db->$collection->deleteOne([
            '_id' => $this->getId(),
        ]);
    }
    public function fromArray(array $arr)
    {
        foreach ($arr as $f => $v) {
            if (property_exists($this, $f)) {
                $this->$f = $v;
            }
        }
    }
    function bsonUnserialize(array $map)
    {
        foreach ($map as $k => $value) {
            $this->$k = $value;
        }
    }
}
