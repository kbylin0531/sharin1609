<?php
namespace {

    use Sharin\Library\Helper\ClientAgent;

    /**
 * 获取客户端IP地址
 * @return string $ip
 */
function get_client_ip(){
    return ClientAgent::getClientIP();
}


// url头部数据
function url_header($url){
    $name = '';$length=0;
    $header = @get_headers($url,true);
    if (!$header) return false;

    if(isset($header['Content-Length'])){
        if(is_array($header['Content-Length'])){
            $length = array_pop($header['Content-Length']);
        }else{
            $length = $header['Content-Length'];
        }
    }
    if(isset($header['Content-Disposition'])){
        if(is_array($header['Content-Disposition'])){
            $dis = array_pop($header['Content-Disposition']);
        }else{
            $dis = $header['Content-Disposition'];
        }
        $i = strpos($dis,"filename=");
        if($i!= false){
            $name = substr($dis,$i+9);
            $name = trim($name,'"');
        }
    }
    if(!$name){
        $name = get_path_this($url);
        if (stripos($name,'?')) $name = substr($name,0,stripos($name,'?'));
        if (!$name) $name = 'index.html';
    }
    // $header['name'] = $name;
    // return $header;
    return array('length'=>$length,'name'=>$name);
}


// url检查
function check_url($url){
    $array = get_headers($url,true);
    if (preg_match('/404/', $array[0])) {
        return false;
    } elseif (preg_match('/403/', $array[0])) {
        return false;
    } else {
        return true;
    }
}

/**
 * 获取网络url文件内容，加入ua，以解决防采集的站
 */
function curl_get_contents($url){
    $ch = curl_init();
    $timeout = 4;
    $user_agent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; WOW64; Trident/4.0; SLCC1)";
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}

//根据扩展名获取mime
function get_file_mime($ext){
    $mimetypes = array(
        '323' => 'text/h323',
        'acx' => 'application/internet-property-stream',
        'ai' => 'application/postscript',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'asf' => 'video/x-ms-asf',
        'asr' => 'video/x-ms-asf',
        'asx' => 'video/x-ms-asf',
        'au' => 'audio/basic',
        'avi' => 'video/x-msvideo',
        'axs' => 'application/olescript',
        'bas' => 'text/plain',
        'bcpio' => 'application/x-bcpio',
        'bin' => 'application/octet-stream',
        'bmp' => 'image/bmp',
        'c' => 'text/plain',
        'cat' => 'application/vnd.ms-pkiseccat',
        'cdf' => 'application/x-cdf',
        'cer' => 'application/x-x509-ca-cert',
        'class' => 'application/octet-stream',
        'clp' => 'application/x-msclip',
        'cmx' => 'image/x-cmx',
        'cod' => 'image/cis-cod',
        'cpio' => 'application/x-cpio',
        'crd' => 'application/x-mscardfile',
        'crl' => 'application/pkix-crl',
        'crt' => 'application/x-x509-ca-cert',
        'csh' => 'application/x-csh',
        'css' => 'text/css',
        'dcr' => 'application/x-director',
        'der' => 'application/x-x509-ca-cert',
        'dir' => 'application/x-director',
        'dll' => 'application/x-msdownload',
        'dms' => 'application/octet-stream',
        'doc' => 'application/msword',
        'docx' => 'application/msword',
        'dot' => 'application/msword',
        'dvi' => 'application/x-dvi',
        'dxr' => 'application/x-director',
        'eps' => 'application/postscript',
        'etx' => 'text/x-setext',
        'evy' => 'application/envoy',
        'exe' => 'application/octet-stream',
        'fif' => 'application/fractals',
        'flr' => 'x-world/x-vrml',
        'gif' => 'image/gif',
        'gtar' => 'application/x-gtar',
        'gz' => 'application/x-gzip',
        'h' => 'text/plain',
        'hdf' => 'application/x-hdf',
        'hlp' => 'application/winhlp',
        'hqx' => 'application/mac-binhex40',
        'hta' => 'application/hta',
        'htc' => 'text/x-component',
        'htm' => 'text/html',
        'html' => 'text/html',
        'htt' => 'text/webviewhtml',
        'ico' => 'image/x-icon',
        'ief' => 'image/ief',
        'iii' => 'application/x-iphone',
        'ins' => 'application/x-internet-signup',
        'isp' => 'application/x-internet-signup',
        'jfif' => 'image/pipeg',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'application/x-javascript',
        'latex' => 'application/x-latex',
        'lha' => 'application/octet-stream',
        'lsf' => 'video/x-la-asf',
        'lsx' => 'video/x-la-asf',
        'lzh' => 'application/octet-stream',
        'm13' => 'application/x-msmediaview',
        'm14' => 'application/x-msmediaview',
        'm3u' => 'audio/x-mpegurl',
        'man' => 'application/x-troff-man',
        'mdb' => 'application/x-msaccess',
        'me' => 'application/x-troff-me',
        'mht' => 'message/rfc822',
        'mhtml' => 'message/rfc822',
        'mid' => 'audio/mid',
        'mny' => 'application/x-msmoney',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mp2' => 'video/mpeg',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mpeg',
        'mpa' => 'video/mpeg',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpp' => 'application/vnd.ms-project',
        'mpv2' => 'video/mpeg',
        'ms' => 'application/x-troff-ms',
        'mvb' => 'application/x-msmediaview',
        'nws' => 'message/rfc822',
        'oda' => 'application/oda',
        'p10' => 'application/pkcs10',
        'p12' => 'application/x-pkcs12',
        'p7b' => 'application/x-pkcs7-certificates',
        'p7c' => 'application/x-pkcs7-mime',
        'p7m' => 'application/x-pkcs7-mime',
        'p7r' => 'application/x-pkcs7-certreqresp',
        'p7s' => 'application/x-pkcs7-signature',
        'pbm' => 'image/x-portable-bitmap',
        'pdf' => 'application/pdf',
        'pfx' => 'application/x-pkcs12',
        'pgm' => 'image/x-portable-graymap',
        'pko' => 'application/ynd.ms-pkipko',
        'pma' => 'application/x-perfmon',
        'pmc' => 'application/x-perfmon',
        'pml' => 'application/x-perfmon',
        'pmr' => 'application/x-perfmon',
        'pmw' => 'application/x-perfmon',
        'png' => 'image/png',
        'pnm' => 'image/x-portable-anymap',
        'pot,' => 'application/vnd.ms-powerpoint',
        'ppm' => 'image/x-portable-pixmap',
        'pps' => 'application/vnd.ms-powerpoint',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.ms-powerpoint',
        'prf' => 'application/pics-rules',
        'ps' => 'application/postscript',
        'pub' => 'application/x-mspublisher',
        'qt' => 'video/quicktime',
        'ra' => 'audio/x-pn-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'ras' => 'image/x-cmu-raster',
        'rgb' => 'image/x-rgb',
        'rmi audio/mid' => 'http://www.dreamdu.com',
        'roff' => 'application/x-troff',
        'rtf' => 'application/rtf',
        'rtx' => 'text/richtext',
        'scd' => 'application/x-msschedule',
        'sct' => 'text/scriptlet',
        'setpay' => 'application/set-payment-initiation',
        'setreg' => 'application/set-registration-initiation',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'sit' => 'application/x-stuffit',
        'snd' => 'audio/basic',
        'spc' => 'application/x-pkcs7-certificates',
        'spl' => 'application/futuresplash',
        'src' => 'application/x-wais-source',
        'sst' => 'application/vnd.ms-pkicertstore',
        'stl' => 'application/vnd.ms-pkistl',
        'stm' => 'text/html',
        'svg' => 'image/svg+xml',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'swf' => 'application/x-shockwave-flash',
        't' => 'application/x-troff',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tgz' => 'application/x-compressed',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'tr' => 'application/x-troff',
        'trm' => 'application/x-msterminal',
        'tsv' => 'text/tab-separated-values',
        'txt' => 'text/plain',
        'uls' => 'text/iuls',
        'ustar' => 'application/x-ustar',
        'vcf' => 'text/x-vcard',
        'vrml' => 'x-world/x-vrml',
        'wav' => 'audio/x-wav',
        'wcm' => 'application/vnd.ms-works',
        'wdb' => 'application/vnd.ms-works',
        'wks' => 'application/vnd.ms-works',
        'wmf' => 'application/x-msmetafile',
        'wps' => 'application/vnd.ms-works',
        'wri' => 'application/x-mswrite',
        'wrl' => 'x-world/x-vrml',
        'wrz' => 'x-world/x-vrml',
        'xaf' => 'x-world/x-vrml',
        'xbm' => 'image/x-xbitmap',
        'xla' => 'application/vnd.ms-excel',
        'xlc' => 'application/vnd.ms-excel',
        'xlm' => 'application/vnd.ms-excel',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.ms-excel',
        'xlt' => 'application/vnd.ms-excel',
        'xlw' => 'application/vnd.ms-excel',
        'xof' => 'x-world/x-vrml',
        'xpm' => 'image/x-xpixmap',
        'xwd' => 'image/x-xwindowdump',
        'z' => 'application/x-compress',
        'zip' => 'application/zip'
    );

    //代码 或文本浏览器输出
    $text = array('oexe','inc','inf','csv','log','asc','tsv');
    $code = array('abap','abc','as','ada','adb','htgroups','htpasswd','conf','htaccess','htgroups',
        'htpasswd','asciidoc','asm','ahk','bat','cmd','c9search_results','cpp','c','cc','cxx','h','hh','hpp',
        'cirru','cr','clj','cljs','CBL','COB','coffee','cf','cson','Cakefile','cfm','cs','css','curly','d',
        'di','dart','diff','patch','Dockerfile','dot','dummy','dummy','e','ejs','ex','exs','elm','erl',
        'hrl','frt','fs','ldr','ftl','gcode','feature','.gitignore','glsl','frag','vert','go','groovy',
        'haml','hbs','handlebars','tpl','mustache','hs','hx','html','htm','xhtml','erb','rhtml','ini',
        'cfg','prefs','io','jack','jade','java','js','jsm','json','jq','jsp','jsx','jl','tex','latex',
        'ltx','bib','lean','hlean','less','liquid','lisp','ls','logic','lql','lsl','lua','lp','lucene',
        'Makefile','GNUmakefile','makefile','OCamlMakefile','make','md','markdown','mask','matlab',
        'mel','mc','mush','mysql','nix','m','mm','ml','mli','pas','p','pl','pm','pgsql','php','phtml',
        'ps1','praat','praatscript','psc','proc','plg','prolog','properties','proto','py','r','Rd',
        'Rhtml','rb','ru','gemspec','rake','Guardfile','Rakefile','Gemfile','rs','sass','scad','scala',
        'scm','rkt','scss','sh','bash','.bashrc','sjs','smarty','tpl','snippets','soy','space','sql',
        'styl','stylus','svg','tcl','tex','txt','textile','toml','twig','ts','typescript','str','vala',
        'vbs','vb','vm','v','vh','sv','svh','vhd','vhdl','xml','rdf','rss',
        'wsdl','xslt','atom','mathml','mml','xul','xbl','xaml','xq','yaml','yml','htm',
        'xib','storyboard','plist','csproj');
    if (array_key_exists($ext,$mimetypes)){
        return $mimetypes[$ext];
    }else{
        if(in_array($ext,$text) || in_array($ext,$code)){
            return "text/plain";
        }
        return 'application/octet-stream';
    }
}
}