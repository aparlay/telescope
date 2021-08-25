<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: media.proto

namespace Aparlay\Core\Microservices\ffmpeg;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>media.RemoveResponse</code>
 */
class RemoveResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string result = 1;</code>
     */
    private $result = '';
    /**
     * Generated from protobuf field <code>string error = 2;</code>
     */
    private $error = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $result
     *     @type string $error
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Media::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string result = 1;</code>
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Generated from protobuf field <code>string result = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setResult($var)
    {
        GPBUtil::checkString($var, True);
        $this->result = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string error = 2;</code>
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Generated from protobuf field <code>string error = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setError($var)
    {
        GPBUtil::checkString($var, True);
        $this->error = $var;

        return $this;
    }

}

