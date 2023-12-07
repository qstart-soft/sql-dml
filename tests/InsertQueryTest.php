<?php

namespace Qstart\Db\Tests;

use PHPUnit\Framework\TestCase;
use Qstart\Db\QueryBuilder\Helper\BindingParamName;
use Qstart\Db\QueryBuilder\Query;

class InsertQueryTest extends TestCase
{
    public function testInsertQuery()
    {
        $query = Query::insert()->into('user')->addValues(['name' => 'John', 'surname' => 'Jonson']);
        $expr = $query->getQueryBuilder()->build();
        $v1 = BindingParamName::getName(BindingParamName::getN() - 1);
        $v2 = BindingParamName::getName(BindingParamName::getN());
        $this->assertSame($expr->getExpression(), "INSERT INTO user (name, surname) VALUES (:$v1, :$v2)");
        $this->assertSame($expr->getParams(), [$v1 => 'John', $v2 => 'Jonson']);

        $query->setStartOfQuery('INSERT IGNORE INTO')->setEndOfQuery('RETURNING id');
        $expr = $query->getQueryBuilder()->build();
        $v1 = BindingParamName::getName(BindingParamName::getN() - 1);
        $v2 = BindingParamName::getName(BindingParamName::getN());
        $this->assertSame($expr->getExpression(), "INSERT IGNORE INTO user (name, surname) VALUES (:$v1, :$v2) RETURNING id");

        $query = Query::insert()->into('user')->addMultipleValues([['name' => 'John', 'surname' => 'Jonson'], ['surname' => 'Nelson', 'name' => 'Mike']]);
        $expr = $query->getQueryBuilder()->build();
        $v1 = BindingParamName::getName(BindingParamName::getN() - 3);
        $v2 = BindingParamName::getName(BindingParamName::getN() - 2);
        $v3 = BindingParamName::getName(BindingParamName::getN() - 1);
        $v4 = BindingParamName::getName(BindingParamName::getN());
        $this->assertSame($expr->getExpression(), "INSERT INTO user (name, surname) VALUES (:$v1, :$v2), (:$v4, :$v3)");
        $this->assertSame($expr->getParams(), [$v1 => 'John', $v2 => 'Jonson', $v3 => 'Nelson', $v4 => 'Mike']);
    }
}
