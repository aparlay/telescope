<?php

// Generated by the protocol buffer compiler.  DO NOT EDIT!
// source: media.proto

namespace Aparlay\Core\Microservices\ffmpeg;

use Aparlay\Core\Microservices\ffmpeg\GPBMetadata\Media;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>media.OptimizeRequest</code>.
 */
class OptimizeRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string src = 1;</code>.
     */
    private $src = '';
    /**
     * Generated from protobuf field <code>string des = 2;</code>.
     */
    private $des = '';
    /**
     * Generated from protobuf field <code>string username = 3;</code>.
     */
    private $username = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $src
     *     @type string $des
     *     @type string $username
     * }
     */
    public function __construct($data = null)
    {
        Media::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string src = 1;</code>.
     * @return string
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * Generated from protobuf field <code>string src = 1;</code>.
     * @param string $var
     * @return $this
     */
    public function setSrc($var)
    {
        GPBUtil::checkString($var, true);
        $this->src = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string des = 2;</code>.
     * @return string
     */
    public function getDes()
    {
        return $this->des;
    }

    /**
     * Generated from protobuf field <code>string des = 2;</code>.
     * @param string $var
     * @return $this
     */
    public function setDes($var)
    {
        GPBUtil::checkString($var, true);
        $this->des = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string username = 3;</code>.
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Generated from protobuf field <code>string username = 3;</code>.
     * @param string $var
     * @return $this
     */
    public function setUsername($var)
    {
        GPBUtil::checkString($var, true);
        $this->username = $var;

        return $this;
    }
}
