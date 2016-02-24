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
	public static function month($i) 
	{
		$i = intval($i);
		return isset(self::$months[$i])?self::$months[$i]:'';
	}
	public static function mysqlDate($date) 
	{
		$date = self::clearDate($date);
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
		if (preg_match('/\d\d\d\d-\d\d-\d\d/', $date)) {
			return self::mysqlToUnix($date);
		}
		if (preg_match('/\d\d\.\d\d\.\d\d\d\d/', $date)) {
			return self::ruToUnix($date);
		}
		if (preg_match('/\d+/', $date)) {
			return $date;
		}
		return false;
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
	
	private static function clearDate( $date )
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