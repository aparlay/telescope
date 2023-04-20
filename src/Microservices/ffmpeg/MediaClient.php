<?php

// GENERATED CODE -- DO NOT EDIT!

namespace Aparlay\Core\Microservices\ffmpeg;

class MediaClient extends \Grpc\BaseStub
{
    /**
     * @param string        $hostname hostname
     * @param array         $opts     channel options
     * @param \Grpc\Channel $channel  (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null)
    {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\DownloadRequest $argument input argument
     * @param array                                              $metadata metadata
     * @param array                                              $options  call options
     *
     * @return \Grpc\UnaryCall
     */
    public function DownloadVideo(
        DownloadRequest $argument,
        $metadata = [],
        $options = []
    ) {
        return $this->_simpleRequest(
            '/media.Media/DownloadVideo',
            $argument,
            ['\Aparlay\Core\Microservices\ffmpeg\DownloadResponse', 'decode'],
            $metadata,
            $options
        );
    }

    /**
     * @param OptimizeRequest $argument input argument
     * @param array           $metadata metadata
     * @param array           $options  call options
     *
     * @return \Grpc\UnaryCall
     */
    public function Quality(OptimizeRequest $argument, $metadata = [], $options = [])
    {
        return $this->_simpleRequest(
            '/media.Media/Quality',
            $argument,
            ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
            $metadata,
            $options
        );
    }

    /**
     * @param OptimizeRequest $argument input argument
     * @param array           $metadata metadata
     * @param array           $options  call options
     *
     * @return \Grpc\UnaryCall
     */
    public function BlackBars(
        OptimizeRequest $argument,
        $metadata = [],
        $options = []
    ) {
        return $this->_simpleRequest(
            '/media.Media/BlackBars',
            $argument,
            ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
            $metadata,
            $options
        );
    }

    /**
     * @param OptimizeRequest $argument input argument
     * @param array           $metadata metadata
     * @param array           $options  call options
     *
     * @return \Grpc\UnaryCall
     */
    public function LowVolume(
        OptimizeRequest $argument,
        $metadata = [],
        $options = []
    ) {
        return $this->_simpleRequest(
            '/media.Media/LowVolume',
            $argument,
            ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
            $metadata,
            $options
        );
    }

    /**
     * @param OptimizeRequest $argument input argument
     * @param array           $metadata metadata
     * @param array           $options  call options
     *
     * @return \Grpc\UnaryCall
     */
    public function Duration(
        OptimizeRequest $argument,
        $metadata = [],
        $options = []
    ) {
        return $this->_simpleRequest(
            '/media.Media/Duration',
            $argument,
            ['\Aparlay\Core\Microservices\ffmpeg\DurationResponse', 'decode'],
            $metadata,
            $options
        );
    }

    /**
     * @param OptimizeRequest $argument input argument
     * @param array           $metadata metadata
     * @param array           $options  call options
     *
     * @return \Grpc\UnaryCall
     */
    public function Trim(
        OptimizeRequest $argument,
        $metadata = [],
        $options = []
    ) {
        return $this->_simpleRequest(
            '/media.Media/Trim',
            $argument,
            ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
            $metadata,
            $options
        );
    }

    /**
     * @param OptimizeRequest $argument input argument
     * @param array           $metadata metadata
     * @param array           $options  call options
     *
     * @return \Grpc\UnaryCall
     */
    public function NormalizeAudio(OptimizeRequest $argument, $metadata = [], $options = [])
    {
        return $this->_simpleRequest(
            '/media.Media/NormalizeAudio',
            $argument,
            ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
            $metadata,
            $options
        );
    }

    /**
     * @param OptimizeRequest $argument input argument
     * @param array           $metadata metadata
     * @param array           $options  call options
     *
     * @return \Grpc\UnaryCall
     */
    public function Watermark(
        OptimizeRequest $argument,
        $metadata = [],
        $options = []
    ) {
        return $this->_simpleRequest(
            '/media.Media/Watermark',
            $argument,
            ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
            $metadata,
            $options
        );
    }

    /**
     * @param OptimizeRequest $argument input argument
     * @param array           $metadata metadata
     * @param array           $options  call options
     *
     * @return \Grpc\UnaryCall
     */
    public function CreateCover(
        OptimizeRequest $argument,
        $metadata = [],
        $options = []
    ) {
        return $this->_simpleRequest(
            '/media.Media/CreateCover',
            $argument,
            ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
            $metadata,
            $options
        );
    }

    /**
     * @param OptimizeRequest $argument input argument
     * @param array           $metadata metadata
     * @param array           $options  call options
     *
     * @return \Grpc\UnaryCall
     */
    public function Optimize(
        OptimizeRequest $argument,
        $metadata = [],
        $options = []
    ) {
        return $this->_simpleRequest(
            '/media.Media/Optimize',
            $argument,
            ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
            $metadata,
            $options
        );
    }

    /**
     * @param UploadRequest $argument input argument
     * @param array         $metadata metadata
     * @param array         $options  call options
     *
     * @return \Grpc\UnaryCall
     */
    public function UploadVideo(
        UploadRequest $argument,
        $metadata = [],
        $options = []
    ) {
        return $this->_simpleRequest(
            '/media.Media/UploadVideo',
            $argument,
            ['\Aparlay\Core\Microservices\ffmpeg\UploadResponse', 'decode'],
            $metadata,
            $options
        );
    }

    /**
     * @param UploadRequest $argument input argument
     * @param array         $metadata metadata
     * @param array         $options  call options
     *
     * @return \Grpc\UnaryCall
     */
    public function UploadCover(
        UploadRequest $argument,
        $metadata = [],
        $options = []
    ) {
        return $this->_simpleRequest(
            '/media.Media/UploadCover',
            $argument,
            ['\Aparlay\Core\Microservices\ffmpeg\UploadResponse', 'decode'],
            $metadata,
            $options
        );
    }

    /**
     * @param RemoveRequest $argument input argument
     * @param array         $metadata metadata
     * @param array         $options  call options
     *
     * @return \Grpc\UnaryCall
     */
    public function Remove(
        RemoveRequest $argument,
        $metadata = [],
        $options = []
    ) {
        return $this->_simpleRequest(
            '/media.Media/Remove',
            $argument,
            ['\Aparlay\Core\Microservices\ffmpeg\RemoveResponse', 'decode'],
            $metadata,
            $options
        );
    }
}
