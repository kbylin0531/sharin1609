<?php
/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Sharin
 * User: asus
 * Date: 8/22/16
 * Time: 10:42 AM
 */
namespace Sharin\Core;
use Sharin\Core;

/**
 * Class Storage
 * @method mixed read(string $filepath, string $file_encoding = null, bool $recursion = false) static 获取文件内容
 * @method array readDir(string $dirpath,bool $recursion=false,bool $_isouter=true) static 获取文件夹下的内容
 * @method int has(string $filepath) static 确定文件或者目录是否存在
 * @method int|bool mtime(string $filepath, int $mtime = null) static 返回文件内容上次的修改时间
 * @method int|false size(string $filepath) static 获取文件按大小
 * @method bool mkdir(string $dirpath,int $auth = 0766) static 创建文件夹
 * @method bool touch(string $filepath,int  $mtime = null,int  $atime = null) static 设定文件的访问和修改时间
 * @method bool chmod(string $filepath,int  $auth = 0755) static 修改文件权限
 * @method bool unlink(string $filepath,bool $recursion = false) static 删除文件,目录时必须保证该目录为空
 * @method bool write(string $filepath,string $content,string $write_encode = null,string $text_encode = 'UTF-8') static 将指定内容写入到文件中
 * @method bool append(string $filepath,string  $content,string $write_encode = null,string $text_encode = 'UTF-8') static 将指定内容追加到文件中
 * @package Sharin
 */
class Storage extends Core {

    const CONF_NAME = 'storage';
    const CONF_CONVENTION = [
        'DRIVER_DEFAULT_INDEX' => 0,
        'DRIVER_CLASS_LIST' => [
            'Sharin\\Core\\Storage\\File',
        ],
        'DRIVER_CONFIG_LIST' => [
        ],
    ];

    /**
     * 目录存在与否
     */
    const IS_DIR    = -1;
    const IS_FILE   = 1;
    const IS_EMPTY  = 0;

//-------------------------------- 特征方法，仅适用于文件系统的驱动 ----------------------------------------------------------------------//
    /**
     * 获取文件权限，以linux的格式显示
     * @static
     * @param string $file
     * @return string|false
     */
    public static function permission($file){
        if(is_readable($file)){
            $perms = fileperms($file);
            if (($perms & 0xC000) == 0xC000) {
                // Socket
                $info = 's';
            } elseif (($perms & 0xA000) == 0xA000) {
                // Symbolic Link
                $info = 'l';
            } elseif (($perms & 0x8000) == 0x8000) {
                // Regular
                $info = '-';
            } elseif (($perms & 0x6000) == 0x6000) {
                // Block special
                $info = 'b';
            } elseif (($perms & 0x4000) == 0x4000) {
                // Directory
                $info = 'd';
            } elseif (($perms & 0x2000) == 0x2000) {
                // Character special
                $info = 'c';
            } elseif (($perms & 0x1000) == 0x1000) {
                // FIFO pipe
                $info = 'p';
            } else {
                // Unknown
                $info = 'u';
            }

            // Owner
            $info .= (($perms & 0x0100) ? 'r' : '-');
            $info .= (($perms & 0x0080) ? 'w' : '-');
            $info .= (($perms & 0x0040) ?
                (($perms & 0x0800) ? 's' : 'x' ) :
                (($perms & 0x0800) ? 'S' : '-'));

            // Group
            $info .= (($perms & 0x0020) ? 'r' : '-');
            $info .= (($perms & 0x0010) ? 'w' : '-');
            $info .= (($perms & 0x0008) ?
                (($perms & 0x0400) ? 's' : 'x' ) :
                (($perms & 0x0400) ? 'S' : '-'));

            // Other
            $info .= (($perms & 0x0004) ? 'r' : '-');
            $info .= (($perms & 0x0002) ? 'w' : '-');
            $info .= (($perms & 0x0001) ?
                (($perms & 0x0200) ? 't' : 'x' ) :
                (($perms & 0x0200) ? 'T' : '-'));
            return $info;
        }else{
            return false;
        }
    }


    /**
     * 文件大小格式化
     * @param int $precision
     * @param int $bytes 文件大小
     * @param int $precision 保留小数点
     * @return string
     */
    public static function formatSize($bytes, $precision = 2){
        if($bytes != 0){
            $unit = [
                'TB' => 1099511627776,  // pow( 1024, 4)
                'GB' => 1073741824,		// pow( 1024, 3)
                'MB' => 1048576,		// pow( 1024, 2)
                'kB' => 1024,			// pow( 1024, 1)
                'B ' => 1,				// pow( 1024, 0)
            ];
            foreach ($unit as $un => $mag) {
                if (doubleval($bytes) >= $mag)//floatval === doubleval
                    return round($bytes / $mag, $precision).' '.$un;
            }
        }
        return "0 B";
    }

    /**
     * 参数一是否是文件
     * @static
     * @param $file
     * @param bool $isfile
     * @return string
     */
    public static function perm($file,$isfile=true){
        $Mode = $isfile?fileperms($file):$file;
        $theMode = ' '.decoct($Mode);
        $theMode = substr($theMode,-4);
        $Owner = array();$Group=array();$World=array();
        if ($Mode &0x1000) $Type = 'p'; // FIFO pipe
        elseif ($Mode &0x2000) $Type = 'c'; // Character special
        elseif ($Mode &0x4000) $Type = 'd'; // Directory
        elseif ($Mode &0x6000) $Type = 'b'; // Block special
        elseif ($Mode &0x8000) $Type = '-'; // Regular
        elseif ($Mode &0xA000) $Type = 'l'; // Symbolic Link
        elseif ($Mode &0xC000) $Type = 's'; // Socket
        else $Type = 'u'; // UNKNOWN

        // Determine les permissions par Groupe
        $Owner['r'] = ($Mode &00400) ? 'r' : '-';
        $Owner['w'] = ($Mode &00200) ? 'w' : '-';
        $Owner['x'] = ($Mode &00100) ? 'x' : '-';
        $Group['r'] = ($Mode &00040) ? 'r' : '-';
        $Group['w'] = ($Mode &00020) ? 'w' : '-';
        $Group['e'] = ($Mode &00010) ? 'x' : '-';
        $World['r'] = ($Mode &00004) ? 'r' : '-';
        $World['w'] = ($Mode &00002) ? 'w' : '-';
        $World['e'] = ($Mode &00001) ? 'x' : '-';

        // Adjuste pour SUID, SGID et sticky bit
        if ($Mode &0x800) $Owner['e'] = ($Owner['e'] == 'x') ? 's' : 'S';
        if ($Mode &0x400) $Group['e'] = ($Group['e'] == 'x') ? 's' : 'S';
        if ($Mode &0x200) $World['e'] = ($World['e'] == 'x') ? 't' : 'T';
        $Mode = $Type.$Owner['r'].$Owner['w'].$Owner['x'].' '.
            $Group['r'].$Group['w'].$Group['e'].' '.
            $World['r'].$World['w'].$World['e'];
        return $Mode.' ('.$theMode.') ';
    }

    /**
     * 拷贝目录
     * 选自Kokexplorer/file.function.php
     * eg:将D:/wwwroot/下面wordpress复制到
     *	D:/wwwroot/www/explorer/0000/del/1/
     * 末尾都不需要加斜杠，复制到地址如果不加源文件夹名，
     * 就会将wordpress下面文件复制到D:/wwwroot/www/explorer/0000/del/1/下面
     * $from = 'D:/wwwroot/wordpress';
     * $to = 'D:/wwwroot/www/explorer/0000/del/1/wordpress';
     *
     * @param string $source
     * @param string $dest
     * @return bool
     */
    public static function copyDir($source, $dest){
        $result = false;
        if (!$dest or $source == substr($dest,0,strlen($source))) return false;//防止父文件夹拷贝到子文件夹，无限递归
        if (is_file($source)) {
            if ($dest[strlen($dest)-1] == '/') {
                $__dest = $dest . "/" . basename($source);
            } else {
                $__dest = $dest;
            }
            $result = copy($source, $__dest);
            chmod($__dest, 0777);
        }elseif (is_dir($source)) {
            if ($dest[strlen($dest)-1] == '/') {
                $dest = $dest . basename($source);
            }
            if (!is_dir($dest)) {
                mkdir($dest,0777);
            }
            if (!$dh = opendir($source)) return false;
            while (($file = readdir($dh)) !== false) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($source . "/" . $file)) {
                        $__dest = $dest . "/" . $file;
                    } else {
                        $__dest = $dest . "/" . $file;
                    }
                    $result = copy_dir($source . "/" . $file, $__dest);
                }
            }
            closedir($dh);
        }
        return $result;
    }

}