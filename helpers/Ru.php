<?php

namespace demetrio77\smartadmin\helpers;

use yii\base\BaseObject;

class Ru extends BaseObject
{
	public static function padeg($number, $variants)
	{
		/*$variants [] =
		 0 =>
		1,21,31,..
		2,3,4,22,23,24,...
		5,6,7,8,9,10-20,25-30,35-40,...*/
		
		if ($number == 0) {
			return str_replace('{n}', $number, $variants[0]);
		}
		elseif ($number % 100 > 10 && $number % 100 < 20) {
			return str_replace('{n}', $number, $variants[3]);
		}
		elseif (in_array($number % 10, [2,3,4])) {
			return str_replace('{n}', $number, $variants[2]);
		}
		elseif ($number % 10 == 1) {
			return str_replace('{n}', $number, $variants[1]);
		}
		else {
			return str_replace('{n}', $number, $variants[3]);
		}
	}

	public static function chislo($number, $variants) {
		if ($number == 0) {
			return str_replace('{n}', $number, $variants[0]);
		}
		elseif ($number==1) {
			return str_replace('{n}', $number, $variants[1]);
		}
		else
			return str_replace('{n}', $number, $variants[2]);
	}
	
	public static function numberToString($num, $zhen=false)
	{
		$ed   = [0=>'ноль',1=>'один',2=>'два',3=>'три',4=>'четыре',5=>'пять',6=>'шесть',7=>'семь',8=>'восемь',9=>'девять', 11=>'одна', 12=>'две'];
		$des  = [2=>'двадцать',3=>'тридцать',4=>'сорок',5=>'пятьдесят',6=>'шестьдесят',7=>'семьдесят',8=>'восемьдесят',9=>'девяносто'];
		$des2 = [10=>'десять',11=>'одиннадцать',12=>'двенадцать',13=>'тринадцать',14=>'четырнадцать',15=>'пятнадцать',16=>'шестнадцать',17=>'семнадцать',18=>'восемнадцать',19=>'девятнадцать'];
		$sot  = [1=>'сто',2=>'двести',3=>'триста',4=>'четыреста',5=>'пятьсот',6=>'шестьсот',7=>'семьсот',8=>'восемьсот',9=>'девятьсот'];
		$mil  = ['', 'миллион','миллиона', 'миллионов'];
		$tys  = ['', 'тысяча','тысячи','тысяч'];
		
		if ($num>1000000000) return $num;
		
		if ($num>0) {
			if ($num>=1000000) {
				$m = floor($num/1000000);
				$ost = $num % 1000000;
				return trim(self::numberToString($m).' '.self::padeg($m, $mil).' '.self::numberToString($ost));
			}
			elseif ($num>=1000) {
				$m = floor($num/1000);
				$ost = $num % 1000;
				return trim(self::numberToString($m, true).' '.self::padeg($m, $tys).' '.self::numberToString($ost));
			}
			elseif ($num>=100) {
				$s = floor($num/100);
				$ost = $num %100;
				return trim((isset($sot[$s])?$sot[$s]:'').' '.self::numberToString($ost, $zhen));
			}
			elseif ($num>19) {
				$s = floor($num/10);
				$ost = $num % 10;
				return trim((isset($des[$s])?$des[$s]:'').' '.self::numberToString($ost, $zhen));
			}
			elseif ($num>=10) {
				return trim($des2[$num]);
			}
			else {
				if ($zhen && $num==1) $num=11;
				elseif ($zhen && $num==2) $num = 12; 
				return trim($ed[$num]);
			}
		}
	}
}