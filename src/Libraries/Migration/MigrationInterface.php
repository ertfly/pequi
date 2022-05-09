<?php

namespace Pequi\Libraries\Migration;

interface MigrationInterface
{
    public function getId();
    public function setId($id);
    public function getValue();
    public function setValue($value);
    public function insert($db);
    public function update($db);
}
