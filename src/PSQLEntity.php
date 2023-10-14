<?php

namespace Pequi;

use Exception;
use Medoo\Medoo;

abstract class PSQLEntity
{
    abstract public function setId($id);
    abstract public function getId();
    abstract public function toArray();

    public function insert(Medoo $db)
    {
        $this->setId(uniqid());
        $db->insert(static::TABLE, $this->toArray());
        return;
    }

    public function update(Medoo $db)
    {
        $db->update(static::TABLE, $this->toArray(), [
            'id' => $this->getId(),
        ]);
    }

    public function delete(Medoo $db)
    {
        $db->update(static::TABLE, ['trash' => true], [
            'id' => $this->getId(),
        ]);
    }

    public function destroy(Medoo $db, $where = [])
    {
        $db->delete(static::TABLE, [
            'id' => $this->getId(),
        ]);
    }

    public function fromJson(array $json)
    {
        foreach ($json as $campo => $valor) {
            $c = $campo;
            $this->$c = $valor;
        }
    }
}
