<?php
namespace Kanti\Domain\Model;

use Kanti\DynamicEntityTrait;

/**
 * @Entity @Table(name="user")
 */
class User
{
    use DynamicEntityTrait;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $password;

    /**
     * @OneToMany(targetEntity="Weight", mappedBy="user")
     * @var Weight[]
     */
    protected $weights;

    /**
     * @ManyToMany(targetEntity="User")
     * @var User[]
     */
    protected $hasAccessTo;
}