<?php

namespace Pequi;

use Medoo\Medoo;
use Pequi\Tools\Strings;

abstract class PSQLEntity
{
    abstract public function setId($id);
    abstract public function getId();
    abstract public function toArray();

    /**
     * Undocumented function
     *
     * @param Medoo $db
     * @return void
     */
    public function insert($db)
    {
        if(!$this->getId()){
            $this->setId(Strings::guidV4());
        }
        
        $db->insert(static::TABLE, $this->toArray());
        return;
    }

    /**
     * Undocumented function
     *
     * @param Medoo $db
     * @return void
     */
    public function update($db)
    {
        $db->update(static::TABLE, $this->toArray(), [
            'id' => $this->getId(),
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Medoo $db
     * @return void
     */
    public function delete($db)
    {
        $db->update(static::TABLE, ['trash' => true], [
            'id' => $this->getId(),
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Medoo $db
     * @return void
     */
    public function destroy($db)
    {
        $db->delete(static::TABLE, [
            'id' => $this->getId(),
        ]);
    }

    /**
     * Undocumented function
     *
     * @param array $json
     * @return void
     */
    public function fromJson(array $json)
    {
        foreach ($json as $campo => $valor) {
            $c = $campo;
            $this->$c = $valor;
        }
    }
}
