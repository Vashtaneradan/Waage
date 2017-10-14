<?php

namespace Kanti\Domain\Model;

use Kanti\DynamicEntityTrait;

/**
 * @Entity @Table(name="wight")
 */
class Weight
{
    use DynamicEntityTrait;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="weights")
     * @var User
     */
    protected $user;

    /**
     * @Column(type="datetime")
     * @var \DateTimeImmutable
     */
    protected $datetime;

    /**
     * @Column(type="float")
     * @var float
     */
    protected $value;
}