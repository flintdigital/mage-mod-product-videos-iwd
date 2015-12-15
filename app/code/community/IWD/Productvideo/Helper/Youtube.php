<?php
class IWD_Productvideo_Helper_Youtube extends Mage_Core_Helper_Data
{
    private static $info;
    private static $id;
    private static $links = array();

    /**
     * @var array $data - Media information about video
     */
    private static $data = array();

    /**
     * @var string $user_agent - useragent for getting data
     * Can be edited
     */
    private static $user_agent = 'Youtube Tools v.1';

    /**
     * @var bool $proxy - Use proxy
     * Can be edited (true, false)
     */
    private static $proxy = true;

    /**
     * @var array $proxy_list - List of the proxy servers
     */
    private static $proxy_list = array();

    /**
     * @var int $proxy_attempts - Number of attempts to use a proxy (Determined automatically)
     */
    private static $proxy_attempts = 0;

    /**
     * @var array $formats - Formats of youtube video
     */
    private static $formats = array(
        '5' => 'flv',
        '6' => 'flv',
        '34' => 'flv',
        '35' => 'flv',
        '18' => 'mp4',
        '22' => 'mp4',
        '37' => 'mp4',
        '38' => 'mp4',
        '83' => 'mp4',
        '82' => 'mp4',
        '85' => 'mp4',
        '84' => 'mp4',
        '43' => 'webm',
        '44' => 'webm',
        '45' => 'webm',
        '46' => 'webm',
        '100' => 'webm',
        '101' => 'webm',
        '102' => 'webm',
        '13' => '3gp',
        '17' => '3gp',
        '36' => '3gp'
    );

    public function getVideoLink($id)
    {
        $this->init($id);
        return $this->get_links();
    }

    public function init($id = null)
    {
        self::$data = self::$links = self::$info = null;
        if (self::$proxy) {
            $dir = realpath(dirname(__FILE__));
            self::$proxy_list = is_file($dir . '/proxy.txt') ? file($dir . '/proxy.txt') : array();
            if (empty(self::$proxy_list)) self::$proxy = false;
            self::$proxy_attempts = sizeof(self::$proxy_list);
        }
        self::$id = $id;
    }

    /**
     * Method for processing getting information about video
     * @param bool $proxy
     * @param int $i
     * @return array|null
     */
    public static function get_info($proxy = false, $i = 0){

        if(empty(self::$id)) die('Enter video id');
        if(!empty(self::$info)) return self::$info;
        # Get video data
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://www.youtube.com/get_video_info?video_id='. self::$id);
        # Use proxy
        if($proxy && self::$proxy){
            $proxy = self::$proxy_list[($i-1)];
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_PROXY, trim($proxy));
        }
        curl_setopt($ch, CURLOPT_USERAGENT, self::$user_agent);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec ($ch);
        curl_close ($ch);

        # Parsing data
        parse_str($data, $info);

        # Check the returned status and, if necessary, use a proxy
        if(@$info['status'] == 'ok') {
            self::$info = $info;
            return $info;
        }
        elseif($i<self::$proxy_attempts && self::$proxy)
            return self::get_info(true, ++$i);
        else
            return false;
    }

    /**
     * Method for getting direct links to video
     * @return array
     */
    public static function get_links(){
        if(!empty(self::$links)) return self::$links;
        if(empty(self::$info)) self::get_info();
        $links_map = explode(',',self::$info['url_encoded_fmt_stream_map']);
        $fmt_list = explode(',',self::$info['fmt_list']);
        if(empty($links_map) || (sizeof($links_map) == 1 && empty($links_map[0]))) return false;
        foreach($links_map as $key => $link){
            parse_str($link,$parts);
            $fmt_parts = explode('/', $fmt_list[$key],3);
            # Create array of information of video
            self::$links[self::$formats[$parts['itag']] .'-'. $fmt_parts[1]] = array(self::$formats[$parts['itag']], $fmt_parts[1], $parts['url']);
        }
        return self::$links;
    }

    public function getUrlParseRegex()
    {
        return "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/";
    }

    public function getRegexMatchIndexOfVideoId()
    {
        return 1;
    }
}
