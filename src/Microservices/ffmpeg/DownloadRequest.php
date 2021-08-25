<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: media.proto

namespace Aparlay\Core\Microservices\ffmpeg;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>media.DownloadRequest</code>
 */
class DownloadRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string bucket_name = 1;</code>
     */
    private $bucket_name = '';
    /**
     * Generated from protobuf field <code>string bucket_id = 2;</code>
     */
    private $bucket_id = '';
    /**
     * Generated from protobuf field <code>string file = 3;</code>
     */
    private $file = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $bucket_name
     *     @type string $bucket_id
     *     @type string $file
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Media::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string bucket_name = 1;</code>
     * @return string
     */
    public function getBucketName()
    {
        return $this->bucket_name;
    }

    /**
     * Generated from protobuf field <code>string bucket_name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setBucketName($var)
    {
        GPBUtil::checkString($var, True);
        $this->bucket_name = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string bucket_id = 2;</code>
     * @return string
     */
    public function getBucketId()
    {
        return $this->bucket_id;
    }

    /**
     * Generated from protobuf field <code>string bucket_id = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setBucketId($var)
    {
        GPBUtil::checkString($var, True);
        $this->bucket_id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string file = 3;</code>
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Generated from protobuf field <code>string file = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setFile($var)
    {
        GPBUtil::checkString($var, True);
        $this->file = $var;

        return $this;
    }

}

