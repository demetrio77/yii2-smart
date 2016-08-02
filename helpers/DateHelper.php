<?php

namespace demetrio77\smartadmin\helpers;

class DateHelper
{
	public static $months = [
		'','Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'
	];
	private static $monthsRod = [
		'','января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'
	];
	private static $daysOfweek = [
		'', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресенье'
	];
	private static $roman = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
		
	public static function getDate( $date, $options=[] )
	{
		if (isset($options['separator'])) $separator = $options['separator'];
		elseif (isset($options['roman']) && $options['roman']) $separator = '.';
		else $separator = ' ';
	
		$date = self::clearDate($date);
	
		$d = explode('-', $date);
		switch (count($d)) {
			case 1:
				if (intval($d[0]))
					return intval($d[0]);
					break;
			case 2:
				$month = self::month($d[1], false, $options);
				return $month.(intval($d[0])? $separator.intval($d[0]):'');
			case 3:
				return
				((intval($d[2]) && intval($d[2])<=31)?intval($d[2]).$separator:'').
				self::month($d[1], true, $options).
				(intval($d[0])?$separator.intval($d[0]):'');
		}
		return false;
	}
	
	public static function month($i, $rod=false, $options=[]) 
	{
		$i = intval($i);
		if ($i<1 || $i>12) return '';
		if (isset($options['roman']) && $options['roman']) {
			return self::$roman[$i];
		}
		return !$rod ? (isset(self::$months[$i])?self::$months[$i]:'') : (isset(self::$monthsRod[$i])?self::$monthsRod[$i]:'');
	}
	
	public static function dayOfWeek($day)
	{
		return isset(self::$daysOfweek[$day])?self::$daysOfweek[$day]:'';
	}
	public static function mysqlDate($date) 
	{
		$date = self::clearDate($date);
		if (!$date) return '';
		$d = explode('-', $date);
		switch (count($d)) {
			case 1:
				if (intval($d[0]))
					return intval($d[0]);
			case 2:
				return self::month(intval($d[1])).(intval($d[0])? ' '.intval($d[0]):'');
			case 3:
				if (!isset(self::$monthsRod[intval($d[1])])) return '';
				return
					((intval($d[2]) && intval($d[2])<=31)?intval($d[2]).' ':'')
					.self::$monthsRod[intval($d[1])].
					(intval($d[0])?' '.intval($d[0]):'');
		}
		return '';		
	}
	
	public static function mysqlShortDate($date) 
	{
		$date = self::clearDate($date);
		$d = explode('-', $date);
		if (count($d)==3) {
			return $d[2].'.'.$d[1].'.'.$d[0];
		}
		return '';
	}
	
	public static function unixDate($u) 
	{
		return date('j',$u).' '.self::month(date('n',$u)).' '.date('Y',$u);
	}
	
	public static function unixShortDate($u) 
	{
		return date('d.m.Y',$u);
	}
	
	public static function mysqlToUnix($date)
	{
		$d = explode('-', $date);
		if (count($d)!=3) return false;
		return mktime(0,0,0,intval($d[1]),intval($d[2]),intval($d[0]));
	}	
	
	public static function ruToUnix($date)
	{
		$d = explode('.', $date);
		if (count($d)!=3) return false;
		return mktime(0,0,0,intval($d[1]),intval($d[0]),intval($d[2]));
	}
	
	public static function ruToMysql($date)
	{
		$d = explode('.', $date);
		if (count($d)!=3) return false;
		return $d[2].'-'.$d[1].'-'.$d[0];
	}
	
	public static function toUnix($date)
	{
		if (preg_match('/$\d\d\d\d-\d\d-\d\d^/', $date)) {
			return self::mysqlToUnix($date);
		}
		if (preg_match('/$\d\d\.\d\d\.\d\d\d\d^/', $date)) {
			return self::ruToUnix($date);
		}
		if (preg_match_all('/(\d\d)\.(\d\d)\.(\d\d\d\d) (\d\d):(\d\d)/', $date, $matches)) {
			if (count($matches)==6) {
				return mktime(intval($matches[4][0]),intval($matches[5][0]),0,intval($matches[2][0]),intval($matches[1][0]),intval($matches[3][0]) );
			}
		}
		if (preg_match('/\d+/', $date)) {
			return $date;
		}
		return false;
	}
	
	public static function dayLimitsUnix($date1, $date2 = 0)
	{
		$start = self::mysqlToUnix($date1);
		
		if (!$date2) {
			$date2 = $date1;
		}
	
		$finish =  self::mysqlToUnix($date2) + 24*60*60;
			
		return [
			'start' => $start,
			'finish' => $finish
		];
	}
	
	public static function monthLimitsUnix($year, $month, $year2=0, $month2 = 0)
	{
		if (!$year2 && !$month2) {
			$year2 = $year; $month2 = $month;
		}
		return [
			'start' => mktime(0,0,0, $month,1,$year),
			'finish' => mktime(0,0,0, $month2+1,1,$year2)
		];
	}
	
	public static function fillIfEmpty( $date )
	{
		$d = explode('-', $date);
		$v = [];
		$v[] = (isset($d[0]) && intval($d[0]))?$d[0]:'00';
		$v[] = (isset($d[1]) && intval($d[1]))?$d[1]:'00';
		$v[] = (isset($d[2]) && intval($d[2]))?$d[2]:'00';
		return implode('-', $v);
	}
	
	public static function clearDate( $date )
	{
		$d = explode('-', $date);
		$v = [];
		if (isset($d[0]) && intval($d[0])) {
			$v[] = $d[0];
		}
		if (isset($d[1]) &&intval($d[1])) {
			$v[] = $d[1];
		}
		if (isset($d[2]) &&intval($d[2])) {
			$v[] =$d[2];
		}
		return implode('-', $v);
	}
}