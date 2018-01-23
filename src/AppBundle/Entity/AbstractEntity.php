<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 18/01/2018
 * Time: 15:34
 */

namespace AppBundle\Entity;

use AppBundle\Services\CodeService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Repository\CategoryRepository;


abstract class AbstractEntity
{
    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=20, unique=true, nullable=true, unique=true)
     */
    protected $code;

    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }


}