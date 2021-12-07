<?php
namespace Aparlay\Core\Api\V1\Dto;

class CreateTipDto
{
    private $cardId;
    private $cardCvv;
    private $message;
    private $amount;
    private $currency;
    private $mediaId;
    private $userIp;


    /**
     * @param $cardId
     * @param $cardCvv
     * @param $amount
     * @param $currency
     * @param $mediaId
     * @param string $message
     */
    public function __construct($cardId, $cardCvv, $amount, $currency, $mediaId, string $message = null)
    {
        $this->cardId = $cardId;
        $this->cardCvv = $cardCvv;
        $this->message = $message;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->mediaId = $mediaId;
    }

    /**
     * @return mixed
     */
    public function getUserIp()
    {
        return $this->userIp;
    }

    /**
     * @param mixed $userIp
     */
    public function setUserIp($userIp): void
    {
        $this->userIp = $userIp;
    }

    /**
     * @return mixed
     */
    public function getMediaId()
    {
        return $this->mediaId;
    }



    /**
     * @return mixed
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * @return mixed
     */
    public function getCardCvv()
    {
        return $this->cardCvv;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}
