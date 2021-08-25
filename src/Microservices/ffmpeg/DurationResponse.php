<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: media.proto

namespace Aparlay\Core\Microservices\ffmpeg;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>media.DurationResponse</code>
 */
class DurationResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string sec = 1;</code>
     */
    private $sec = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $sec
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Media::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string sec = 1;</code>
     * @return string
     */
    public function getSec()
    {
        return $this->sec;
    }

    /**
     * Generated from protobuf field <code>string sec = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setSec($var)
    {
        GPBUtil::checkString($var, True);
        $this->sec = $var;

        return $this;
    }

}
