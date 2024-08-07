<?php
namespace Mrweb;
require_once 'exceptions.php';

class Mrweb_API {
    private $apikey;

    public function __construct($apikey = null, $use_testkey = false, $env = false) {
        $this->apikey = $apikey;
        if ($use_testkey) {
            $this->apikey = 'testkey';
        } elseif ($env) {
            try {
                $this->apikey = getenv('MRWEB_APIKEY');
            } catch (Exception $e) {
                throw new EnvError('Failed To Get APIKEY From env please Set By Name MRWEB_APIKEY in environ variable name');
            }
        }
    }

    public function translate($to, $text) {
        $parms = array('to' => $to, 'text' => $text);
        $api = file_get_contents("https://mrapiweb.ir/api/translate.php?" . http_build_query($parms));
        $result = json_decode($api, true);
        try {
            return $result['translate'];
        } catch (Exception $e) {
            throw new APIError("Translate Error For Lang {$to}");
        }
    }

    public function ocr($to, $url) {
        $api = file_get_contents("https://mrapiweb.ir/api/ocr.php?url={$url}&lang={$to}");
        $result = json_decode($api, true);
        try {
            return $result['result'];
        } catch (Exception $e) {
            throw new APIError("Error In OCR Lang {$to}");
        }
    }

    public function isbadword($text) {
        $text = "text=" . urlencode($text);
        $api = file_get_contents("https://mrapiweb.ir/api/badword.php?{$text}");
        $result = json_decode($api, true);
        return $result['isbadword'] == true;
    }

    public function randbio() {
        return file_get_contents('https://mrapiweb.ir/api/bio.php');
    }

    public function isaitext($text) {
        $text = "text=" . urlencode($text);
        $api = file_get_contents("https://mrapiweb.ir/api/aitext.php?{$text}");
        $result = json_decode($api, true);
        return $result['aipercent'] != '0%';
    }

    public function notebook($text, $savetofile = false, $filename = null) {
        $text = str_replace(' ', '-', $text);
        $api = file_get_contents("https://mrapiweb.ir/api/notebook.php?text={$text}");
        if ($savetofile) {
            if (is_null($filename)) {
                throw new Exception('Filename Is Required!');
            }
            file_put_contents($filename, $api);
        } else {
            return $api;
        }
    }

    public function email($to, $subject, $text) {
        $send = "to={$to}&subject={$subject}&message={$text}";
        file_get_contents("https://mrapiweb.ir/api/email.php?{$send}");
        return "Email Sent To {$to}";
    }

    public function ipinfo($ip) {
        $api = file_get_contents("https://mrapiweb.ir/api/ipinfo.php?ipaddr={$ip}");
        $ip = json_decode($api, true);
        try {
            return $ip;
        } catch (Exception $e) {
            throw new APIError("Failed To Get This IP Information : {$ip}");
        }
    }

    public function insta($link) {
        return str_replace('instagram.com', 'ddinstagram.com', $link);
    }

    public function voicemaker($text, $sayas = 'man', $filename = null) {
        $text = str_replace(' ', '-', $text);
        $api = file_get_contents("https://mrapiweb.ir/api/voice.php?sayas={$sayas}&text={$text}");
        if (is_null($filename)) {
            throw new Exception('Filename Is Required!');
        }
        file_put_contents($filename, $api);
        return true;
    }

    public function imagegen($text) {
        $apikey = $this->apikey;
        $text = str_replace(' ', '-', $text);
        return file_get_contents("https://mrapiweb.ir/api/imagegen.php?key={$apikey}&imgtext={$text}");
    }

    public function proxy() {
        $api = file_get_contents('https://mrapiweb.ir/api/telproxy.php');
        $proxy = json_decode($api, true);
        return $proxy['connect'];
    }

    public function fal($filename) {
        $api = file_get_contents('https://mrapiweb.ir/api/fal.php');
        file_put_contents($filename, $api);
        return true;
    }

    public function worldclock() {
        return file_get_contents('https://mrapiweb.ir/api/zone.php');
    }

    public function youtube($vid) {
        $api = file_get_contents("https://mrapiweb.ir/api/yt.php?key={$this->apikey}&id={$vid}");
        return $api;
    }

    public function sendweb3($privatekey = null, $address = null, $amount = null, $rpc = null, $chainid = null) {
        $api = file_get_contents("https://mrapiweb.ir/api/wallet.php?key={$privatekey}&address={$address}&amount={$amount}&rpc={$rpc}&chainid={$chainid}");
        return $api;
    }

    public function google_drive($link) {
        $api = file_get_contents("https://mrapiweb.ir/api/gdrive.php?url={$link}");
        $drive = json_decode($api, true);
        return $drive['link'];
    }

    public function bing_dalle($text) {
        throw new EndSupport('Bing Dalle Is End Of Support');
    }

    public function wikipedia($text) {
        return file_get_contents("https://mrapiweb.ir/wikipedia/?find={$text}&lang=fa");
    }

    public function chrome_extention($id, $file) {
        $api = file_get_contents("https://mrapiweb.ir/api/chrome.php?id={$id}");
        file_put_contents($file, $api);
    }

    public function fakesite($site) {
        $api = file_get_contents("https://mrapiweb.ir/api/fakesite.php?site={$site}");
        return json_decode($api, true)['is_real'];
    }

    public function webshot($site, $filesave) {
        $apikey = $this->apikey;
        $api1 = file_get_contents("https://mrapiweb.ir/api/webshot.php?key={$apikey}&url={$site}&fullSize=false&height=512&width=512");
        try {
            file_put_contents($filesave, $api1);
        } catch (Exception $e) {
            return $api1;
        }
    }

    public function barcode($code) {
        $apikey = $this->apikey;
        $api = file_get_contents("https://mrapiweb.ir/api/barcode.php?key={$apikey}&code={$code}");
        try {
            return json_decode($api, true)['result'];
        } catch (Exception $e) {
            return json_decode($api, true)['message'];
        }
    }

    public function domain_check($domain) {
        $api = json_decode(file_get_contents("https://mrapiweb.ir/api/domain.php?domain={$domain}"), true);
        return $api;
    }

    public function qr($texturl, $action = 'encode', $savefile = true) {
        if ($action == 'encode') {
            $text = "action={$action}&text={$texturl}";
            $api = file_get_contents("https://mrapiweb.ir/api/qr/qrcode.php?{$text}");
            if ($savefile) {
                file_put_contents('qr.png', $api);
            } else {
                return $api;
            }
        } else {
            $text = "action={$action}&url={$texturl}";
            $api = file_get_contents("https://mrapiweb.ir/api/qr/qrcode.php?{$text}");
            return $api;
        }
    }
}

class Mrweb_AI {
    private $version;

    public function __construct() {
        $this->version = "1.7";
    }

    public function bard($query) {
        try {
            $result = file_get_contents("https://mrapiweb.ir/bardai/ask?text=" . urlencode($query));
            return $result;
        } catch (Exception $e) {
            throw new AIError("Failed To Get Response From Bard", $e);
        }
    }

    public function gpt($query) {
        $query = urlencode($query);
        try {
            return file_get_contents("https://mrapiweb.ir/ai/?{$query}");
        } catch (Exception $e) {
            throw new AIError("Failed To Get Answer. Make Sure That You Are Connected To Internet & VPN is off", null);
        }
    }

    public function evilgpt($query) {
        throw new EndSupport("EvilGPT Is End Of Support", null);
    }

    public function gemini($query) {
        $query = urlencode($query);
        $api = file_get_contents("https://mrapiweb.ir/api/geminiai.php?{$query}");
        try {
            return $api;
        } catch (Exception $e) {
            throw new AIError("No Answer Found From Gemini. Please Try Again!", null);
        }
    }

    public function codeai($query) {
        $query = urlencode($query);
        $api = file_get_contents("https://mrapiweb.ir/api/aiblack.php?{$query}");
        try {
            return $api;
        } catch (Exception $e) {
            throw new AIError("No Answer Found From CodeAI. Please Try Again!", null);
        }
    }

    public function gemma($query) {
        $query = urlencode($query);
        $api = file_get_contents("https://mrapiweb.ir/chatbot/newrouter.php?{$query}");
        try {
            return $api;
        } catch (Exception $e) {
            throw new AIError("No Answer Found From Gemma. Please Try Again!", null);
        }
    }

    public function zzzcode($prompt, $language = "python", $mode = "normal") {
        try {
            $query = http_build_query(array("question" => $prompt, "lang" => $language, "mode" => $mode));
            return file_get_contents("https://mrapiweb.ir/chatbot/zzzcode.php?{$query}");
        } catch (Exception $e) {
            throw new AIError("No Answer Found From Zzzcode. Please Try Again!", null);
        }
    }
}

class Mrweb_FAKEMAIL {
    private $version;

    public function __construct() {
        $this->version = "1.7";
    }

    public function create() {
        $response = file_get_contents("https://mrapiweb.ir/api/fakemail.php?method=getNewMail");
        return json_decode($response, true)["results"]["email"];
    }

    public function getmails($email) {
        $response = file_get_contents("https://mrapiweb.ir/api/fakemail.php?method=getMessages&email={$email}");
        return json_decode($response, true)["results"];
    }
}

class Mrweb_HashCheck {
    private $version;

    public function __construct() {
        $this->version = "1.7";
    }

    public function tron($thash) {
        $api = file_get_contents("https://mrapiweb.ir/api/cryptocheck/tron.php?hash={$thash}");
        $tron = json_decode($api, true);
        return $tron;
    }

    public function tomochain($thash) {
        $api = file_get_contents("https://mrapiweb.ir/api/cryptocheck/tomochain.php?hash={$thash}");
        $tomo = json_decode($api, true);
        return $tomo;
    }
}

class Mrweb_TRON {
    private $version;

    public function __construct() {
        $this->version = "1.7";
    }

    public function generate() {
        $api = json_decode(file_get_contents("https://mrapiweb.ir/api/tronapi.php?action=genaddress"), true);
        return $api;
    }

    public function balance($address) {
        $api = json_decode(file_get_contents("https://mrapiweb.ir/api/tronapi.php?action=getbalance&address={$address}"), true);
        return $api["balance"];
    }

    public function info($address) {
        $api = json_decode(file_get_contents("https://mrapiweb.ir/api/tronapi.php?action=addressinfo&address={$address}"), true);
        return $api;
    }

    public function send($key, $fromadd, $to, $amount) {
        $api = json_decode(file_get_contents("https://mrapiweb.ir/api/tronapi.php?action=sendtrx&key={$key}&fromaddress={$fromadd}&toaddress={$to}&amount={$amount}"), true);
        return $api;
    }
}
?>