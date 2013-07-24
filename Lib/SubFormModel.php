<?php


namespace pingdecopong\PDPGeneratorBundle\Lib;

/**
 * Class SubFormModel
 * TODO:UTILバンドルに移動
 */
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