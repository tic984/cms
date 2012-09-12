<?php
class I18N
{
	private static $_instance_pools = array();

	private static $_locale;

	private static function localeHasChanged($locale)
	{
		if(self::$_locale != $locale)
		{
			self::$_locale == $locale;
			self::$_instance_pools = array();
			return true;
		}
		return false;
	}


	public static function getInstance($module='', $locale='')
	{
		$module = ($module=='') ? 'application' : $module;
		$locale = ($locale =='') ? 'it_IT' : $locale;

		if(self::localeHasChanged($locale) || self::$instance[$module] == null)
		self::$_instance_pools[$module] = new Dictionary($module);

		return self::$_instance_pools[$module];
	}

	private function aggiungiLabel($label)
	{
		$path_to_dic = APP_BASE_PATH.self::DIC_PATH_PREFIX.self::$locale."/dic.res";

		if($label!='') {
			$f = fopen($path_to_dic, "a");
			fwrite($f, sprintf("\n%s=\"%s\"\n", $label, $label));
			fclose($f);
		}
	}

	public function t($label)
	{
		$label = trim($label);
		if(isset($this->dic[$label]))
		return $this->dic[$label];
		$this->aggiungiLabel($label);
		return $label;
	}
	public function pt($label)
	{
		print(t($label));
	}

	/** FROM MYSQL TO LOCALE **/
	public function formatDate($mysqldate)
	{
		$time = date2timestamp($mysqldate);
		if(!$time) return '';
		return strftime($this->t('converter.date_format'), $time);
	}
	public function formatTimestamp($timestamp)
	{
		return strftime($this->t('converter.date_format'), datetime2timestamp($timestamp));
	}

	public function formatDateTime($mysqldatetime)
	{
		return strftime($this->t('converter.full_date_format'), datetime2timestamp($mysqldate));
	}
	/** FROM MYSQL TO LOCALE **/

	/** FROM LOCALE TO MYSQL **/
	public function locale2mysqldate($locale_date)
	{
		$arr = strptime($locale_date, $this->t('converter.date_format'));
		if($arr===false)
		return '00-00-0000 00:00:00';
		return sprintf("%4s-%02s-%02s 00:00:00", 1900 + $arr['tm_year'], $arr['tm_mon']+1,$arr['tm_mday'], 0, 0, 0);
	}

	public function locale2mysqldatetime($locale_date_time)
	{
		$arr = strptime($locale_date_time, $this->t('converter.full_date_format'));
		if($arr===false)
		return '00-00-0000 00:00:00';
		return sprintf("%4s-%02s-%02s %02s:%02s:%02s", 1900 + $arr['tm_year'], $arr['tm_mon']-1 ,$arr['tm_mday'], $arr['tm_hour'],$arr['tm_min'], $arr['tm_sec']);
	}
	/** FROM LOCALE TO MYSQL **/

	public function sortLocaleLabels($lang='')
	{
		$lang = ($lang=='' ) ? Core::getInstance()->getVisualizza()->getLingua() : $lang;
		$path_to_dic = APP_BASE_PATH.self::DIC_PATH_PREFIX.$lang."/dic.res";

		$content = parse_ini_file($path_to_dic);
		ksort($content);
		$new_content = '';
		foreach($content as $k=>$v)
		$new_content.=sprintf("%s=\"%s\"\n", $k, $v );
		file_put_contents($path_to_dic, $new_content);
	}
}
?>