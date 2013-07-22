<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fhirashima
 * Date: 13/07/22
 * Time: 16:14
 * To change this template use File | Settings | File Templates.
 */

namespace pingdecopong\PDPGeneratorBundle\Lib;


class SubFormModel {

    /**
     * @var string
     */
    private $buttonAction;

    /**
     * @var string
     */
    private $returnAddress;

    /**
     * @param string $buttonAction
     */
    public function setButtonAction($buttonAction)
    {
        $this->buttonAction = $buttonAction;
    }

    /**
     * @return string
     */
    public function getButtonAction()
    {
        return $this->buttonAction;
    }

    /**
     * @param string $returnAddress
     */
    public function setReturnAddress($returnAddress)
    {
        $this->returnAddress = $returnAddress;
    }

    /**
     * @return string
     */
    public function getReturnAddress()
    {
        return $this->returnAddress;
    }

}