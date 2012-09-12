<?php
class WeDo_Libs_TimThumb
{
    const RELPATH = '/libs/timthumb/tt.php';
    
    public static function getLink($src, $w=0, $h=0, $zc=0)
    {
        $code = sprintf("%s?src=%s", self::RELPATH, $src );
        if($w != 0)
            $code .= sprintf("&w=%d", $w);
        if($h != 0)
            $code .= sprintf("&h=%d", $h);
        if($zc != 0)
            $code .= sprintf("&zc=%d", $zc);
        return $code;
    }
}
?>
