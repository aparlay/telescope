<?php

namespace Aparlay\Core\Admin\Services;

/**
 * User Agent class.
 */
class UserAgent
{
    protected $agent;
    protected $os;
    protected $browser;
    protected $prefix;
    protected $version;
    protected $engine;
    protected $device = 'Desktop';
    protected $isBot = false;

    protected $oss = [
        'Android' => ['Android'],
        'Linux' => ['linux', 'Linux'],
        'Mac OS X' => ['Macintosh', 'Mac OS X'],
        'iOS' => ['like Mac OS X'],
        'Windows' => ['Windows NT', 'win32'],
        'Windows Phone' => ['Windows Phone'],
        'Chrome OS' => ['CrOS'],
    ];
    protected $browsers = [
        'Waptap App' => ['Waptap'],
        'Apple Safari' => ['Safari'],
        'Google Chrome' => ['Chrome'],
        'Edge' => ['Edge'],
        'Internet Explorer' => ['MSIE'],
        'Mozilla Firefox' => ['Firefox'],
        'Opera' => ['OPR', 'Opera'],
        'Netscape' => ['Netscape'],
        'cURL' => ['curl'],
        'Wget' => ['Wget'],
    ];
    protected $engines = [
        'OKHttp' => ['OkHttp'],
        'Gecko' => ['Gecko'],
        'Blink' => ['AppleWebKit'],
        'WebKit' => ['X) AppleWebKit'],
        'EdgeHTML' => ['Edge'],
        'Trident' => ['Trident', 'MSIE'],
    ];
    protected $devices = [
        'iPad' => ['iPad'],
        'iPhone' => ['iPhone'],
        'Samsung' => ['SAMSUNG', 'SM-G'],
        'Sony Xperia' => ['G8231', 'E6653'],
        'Amazon Kindle' => ['Kindle'],
        'Nintendo 3DS' => ['Nintendo 3DS'],
        'Nintendo Wii U' => ['Nintendo WiiU'],
        'Playstation Vita' => ['Playstation Vita'],
        'Playstation 4' => ['Playstation 4'],
        'Xbox One' => ['Xbox One'],
        'Xbox One S' => ['XBOX_ONE_ED'],
        'Apple TV' => ['AppleTV'],
        'Google Nexus Player' => ['Nexus Player'],
        'Amazon Fire TV' => ['AFTS'],
        'Chromecast' => ['CrKey'],
        'HTC' => ['HTC'],
        'OnePlus' => ['OnePlus'],
    ];
    protected $bots = [
        'Baidu' => ['Baidu', 'Baiduspider'],
        'BingBot' => ['bingbot', 'Bingbot'],
        'DuckDuckGo' => ['DuckDuckBot'],
        'Googlebot' => ['Googlebot'],
        'Yahoo!' => ['Slurp'],
        'Yandex' => ['Yandex', 'YandexBot'],
        'Telegram' => ['TelegramBot'],
        'Twitter' => ['TwitterBot'],
        'Sogou' => ['Sogou'],
        'Exabot' => ['Exabot', 'Konqueror'],
        'Facebook' => ['facebot', 'facebookexternalhit'],
        'Alexa' => ['ia_archiver'],
    ];

    public function __construct($agent = null)
    {
        if (! $agent && isset($_SERVER['HTTP_USER_AGENT'])) {
            $agent = $_SERVER['HTTP_USER_AGENT'];
        }

        $this->agent = $agent;

        $this->parse();
    }

    public function parse()
    {
        // Find OS
        foreach ($this->oss as $os => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($this->agent, $pattern) !== false) {
                    $this->os = $os;
                    break;
                }
            }
        }

        // Find browser
        foreach ($this->browsers as $browser => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($this->agent, $pattern) !== false) {
                    $this->browser = $browser;
                    $this->prefix = $pattern;
                    break;
                }
            }
        }

        // Engine
        foreach ($this->engines as $engine => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($this->agent, $pattern) !== false) {
                    $this->engine = $engine;
                    break;
                }
            }
        }

        // Device
        foreach ($this->devices as $device => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($this->agent, $pattern) !== false) {
                    $this->device = $device;
                    break;
                }
            }
        }

        // Browser version
        $pattern = '#(?<browser>'.implode('|', ['Version', $this->prefix, 'other']).')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        preg_match_all($pattern, $this->agent, $matches);

        $this->version = $matches['version'][0];

        if (count($matches['browser']) != 1) {
            $this->version = strripos($this->agent, 'Version') < strripos($this->agent, $this->prefix) ? $matches['version'][0] : $matches['version'][1];
        }

        // Check if is a BOT
        foreach ($this->bots as $bot => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($this->agent, $pattern) !== false) {
                    $this->isBot = true;
                    $this->device = 'BOT';
                    break;
                }
            }
        }
    }

    public function setAgent($agent)
    {
        $this->agent = $agent;
        $this->parse();
    }

    public function isBot()
    {
        return $this->isBot;
    }

    public function getInfo()
    {
        return [
            'agent' => $this->getAgent(),
            'device' => $this->getDevice(),
            'os' => $this->getOS(),
            'browser' => $this->getBrowser(),
            'engine' => $this->getEngine(),
            'prefix' => $this->getPrefix(),
            'version' => $this->getVersion(),
            'is_bot' => $this->isBot() ? 'true' : 'false',
        ];
    }

    public function getDevice()
    {
        return $this->device;
    }

    public function getEngine()
    {
        return $this->engine;
    }

    public function getAgent()
    {
        return $this->agent;
    }

    public function getOS()
    {
        return $this->os;
    }

    public function getBrowser()
    {
        return $this->browser;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
