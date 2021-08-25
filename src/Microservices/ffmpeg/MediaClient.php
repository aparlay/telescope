<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Aparlay\Core\Microservices\ffmpeg;

/**
 */
class MediaClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\DownloadRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DownloadVideo(\Aparlay\Core\Microservices\ffmpeg\DownloadRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/media.Media/DownloadVideo',
        $argument,
        ['\Aparlay\Core\Microservices\ffmpeg\DownloadResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Quality(\Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/media.Media/Quality',
        $argument,
        ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function BlackBars(\Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/media.Media/BlackBars',
        $argument,
        ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function LowVolume(\Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/media.Media/LowVolume',
        $argument,
        ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Duration(\Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/media.Media/Duration',
        $argument,
        ['\Aparlay\Core\Microservices\ffmpeg\DurationResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Trim(\Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/media.Media/Trim',
        $argument,
        ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function NormalizeAudio(\Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/media.Media/NormalizeAudio',
        $argument,
        ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Watermark(\Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/media.Media/Watermark',
        $argument,
        ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CreateCover(\Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/media.Media/CreateCover',
        $argument,
        ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Optimize(\Aparlay\Core\Microservices\ffmpeg\OptimizeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/media.Media/Optimize',
        $argument,
        ['\Aparlay\Core\Microservices\ffmpeg\OptimizeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\UploadRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function UploadVideo(\Aparlay\Core\Microservices\ffmpeg\UploadRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/media.Media/UploadVideo',
        $argument,
        ['\Aparlay\Core\Microservices\ffmpeg\UploadResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\UploadRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function UploadCover(\Aparlay\Core\Microservices\ffmpeg\UploadRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/media.Media/UploadCover',
        $argument,
        ['\Aparlay\Core\Microservices\ffmpeg\UploadResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Aparlay\Core\Microservices\ffmpeg\RemoveRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Remove(\Aparlay\Core\Microservices\ffmpeg\RemoveRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/media.Media/Remove',
        $argument,
        ['\Aparlay\Core\Microservices\ffmpeg\RemoveResponse', 'decode'],
        $metadata, $options);
    }

}
