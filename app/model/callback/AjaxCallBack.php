<?php

namespace app\model\callback;


class AjaxCallBack
{
    const UNSUCCESS = 0;
    const SUCCESS = 1;

    private $success;
    private $messages;
    private $data;

    /**
     * Vytvoří novou instanci callBack zprávy.
     */
    public function __construct()
    {
        $this->success = AjaxCallBack::SUCCESS;
        $this->messages = array();
        $this->data = array();
    }

    /**
     * Nastaví příznak, že požadavek nebyl úspěšný.
     */
    public function setFail() {
        $this->success = AjaxCallBack::UNSUCCESS;
    }

    /**
     * Přidá hlášku o requestu.
     * @param CallBackMessage $callBackMessage
     */
    public function addMessage(CallBackMessage $callBackMessage)
    {
        $this->messages[] = json_encode($callBackMessage->toArray());
    }

    /**
     * Přidá více zpráv najednou.
     * @param array $messages Pole zpráv.
     */
    public function addMessages($messages) {
        foreach($messages as $message) {
            $this->addMessage($message);
        }
    }

    /**
     * Přidá data, která putujou ke klientovi.
     * @param CallBackData $data array Pole dat.
     * @param bool $jsonEncode False, pokud se nemají data enkodovat do jsonu, výchozi je true.
     */
    public function addData(CallBackData $data, $jsonEncode = true)
    {
        if($jsonEncode)
            $this->data[$data->getKey()] = json_encode($data->getValue());
        else
            $this->data[$data->getKey()] = $data->getValue();
    }

    /**
     * Sestaví výslednou zprávu a zakóduje do JSON.
     * @return string Zakódovaný response.
     */
    public function buildMessage()
    {
        return json_encode([
            'success' => $this->success,
            'messages'=> $this->messages,
            'data' => $this->data
        ]);
    }
}