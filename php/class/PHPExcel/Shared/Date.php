<? class PHPExcel_Shared_Date{const CALENDAR_WINDOWS_1900=1900;const CALENDAR_MAC_1904=1904;public static $numberSuffixes=array('st','nd','rd','th',);protected static $excelBaseDate=self::CALENDAR_WINDOWS_1900;public static function setExcelCalendar($baseDate){if(($baseDate==self::CALENDAR_WINDOWS_1900)||($baseDate==self::CALENDAR_MAC_1904)){self::$excelBaseDate=$baseDate;return true;}return false;}public static function getExcelCalendar(){return self::$excelBaseDate;}public static function ExcelToPHP($dateValue=0,$adjustToTimezone=false,$timezone=null){if(self::$excelBaseDate==self::CALENDAR_WINDOWS_1900){$myexcelBaseDate=25569;if($dateValue<60){--$myexcelBaseDate;}}else{$myexcelBaseDate=24107;}if($dateValue>=1){$utcDays=$dateValue-$myexcelBaseDate;$returnValue=round($utcDays*86400);if(($returnValue<=PHP_INT_MAX)&&($returnValue>=-PHP_INT_MAX)){$returnValue=(integer)$returnValue;}}else{$hours=round($dateValue*24);$mins=round($dateValue*1440)-round($hours*60);$secs=round($dateValue*86400)-round($hours*3600)-round($mins*60);$returnValue=(integer)gmmktime($hours,$mins,$secs);}$timezoneAdjustment=($adjustToTimezone)? PHPExcel_Shared_TimeZone::getTimezoneAdjustment($timezone,$returnValue):0;return $returnValue+$timezoneAdjustment;}public static function ExcelToPHPObject($dateValue=0){$dateTime=self::ExcelToPHP($dateValue);$days=floor($dateTime/86400);$time=round((($dateTime/86400)-$days)*86400);$hours=round($time/3600);$minutes=round($time/60)-($hours*60);$seconds=round($time)-($hours*3600)-($minutes*60);$dateObj=date_create('1-Jan-1970+'.$days.' days');$dateObj->setTime($hours,$minutes,$seconds);return $dateObj;}public static function PHPToExcel($dateValue=0,$adjustToTimezone=false,$timezone=null){$saveTimeZone=date_default_timezone_get();date_default_timezone_set('UTC');$timezoneAdjustment=($adjustToTimezone)? PHPExcel_Shared_TimeZone::getTimezoneAdjustment($timezone ? $timezone:$saveTimeZone,$dateValue):0;$retValue=false;if((is_object($dateValue))&&($dateValue instanceof DateTime)){$dateValue->add(new DateInterval('PT'.$timezoneAdjustment.'S'));$retValue=self::FormattedPHPToExcel($dateValue->format('Y'),$dateValue->format('m'),$dateValue->format('d'),$dateValue->format('H'),$dateValue->format('i'),$dateValue->format('s'));}elseif(is_numeric($dateValue)){$dateValue+=$timezoneAdjustment;$retValue=self::FormattedPHPToExcel(date('Y',$dateValue),date('m',$dateValue),date('d',$dateValue),date('H',$dateValue),date('i',$dateValue),date('s',$dateValue));}elseif(is_string($dateValue)){$retValue=self::stringToExcel($dateValue);}date_default_timezone_set($saveTimeZone);return $retValue;}public static function FormattedPHPToExcel($year,$month,$day,$hours=0,$minutes=0,$seconds=0){if(self::$excelBaseDate==self::CALENDAR_WINDOWS_1900){$excel1900isLeapYear=true;if(($year==1900)&&($month<=2)){$excel1900isLeapYear=false;}$myexcelBaseDate=2415020;}else{$myexcelBaseDate=2416481;$excel1900isLeapYear=false;}if($month>2){$month-=3;}else{$month+=9;--$year;}$century=substr($year,0,2);$decade=substr($year,2,2);$excelDate=floor((146097*$century)/4)+floor((1461*$decade)/4)+floor((153*$month+2)/5)+$day+1721119-$myexcelBaseDate+$excel1900isLeapYear;$excelTime=(($hours*3600)+($minutes*60)+$seconds)/86400;return(float)$excelDate+$excelTime;}public static function isDateTime(PHPExcel_Cell $pCell){return self::isDateTimeFormat($pCell->getWorksheet()->getStyle($pCell->getCoordinate())->getNumberFormat());}public static function isDateTimeFormat(PHPExcel_Style_NumberFormat $pFormat){return self::isDateTimeFormatCode($pFormat->getFormatCode());}private static $possibleDateFormatCharacters='eymdHs';public static function isDateTimeFormatCode($pFormatCode=''){if(strtolower($pFormatCode)===strtolower(PHPExcel_Style_NumberFormat::FORMAT_GENERAL)){return false;}if(preg_match('/[0#]E[+-]0/i',$pFormatCode)){return false;}switch($pFormatCode){case  PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYSLASH:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYMINUS:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_DMMINUS:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_MYMINUS:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME1:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME2:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME5:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME6:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME7:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME8:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX16:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX17:case  PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX22:return true;}if((substr($pFormatCode,0,1)=='_')||(substr($pFormatCode,0,2)=='0 ')){return false;}if(preg_match('/(^|\])[^\[]*['.self::$possibleDateFormatCharacters.']/i',$pFormatCode)){if(strpos($pFormatCode,'"')!==false){$segMatcher=false;foreach(explode('"',$pFormatCode)as $subVal){if(($segMatcher=!$segMatcher)&&(preg_match('/(^|\])[^\[]*['.self::$possibleDateFormatCharacters.']/i',$subVal))){return true;}}return false;}return true;}return false;}public static function stringToExcel($dateValue=''){if(strlen($dateValue)<2){return false;}if(!preg_match('/^(\d{1,4}[\.\/\-][A-Z]{3,9}([\.\/\-]\d{1,4})?|[A-Z]{3,9}[\.\/\-]\d{1,4}([\.\/\-]\d{1,4})?|\d{1,4}[\.\/\-]\d{1,4}([\.\/\-]\d{1,4})?)(\d{1,2}:\d{1,2}(:\d{1,2})?)?$/iu',$dateValue)){return false;}$dateValueNew=PHPExcel_Calculation_DateTime::DATEVALUE($dateValue);if($dateValueNew===PHPExcel_Calculation_Functions::VALUE()){return false;}if(strpos($dateValue,':')!==false){$timeValue=PHPExcel_Calculation_DateTime::TIMEVALUE($dateValue);if($timeValue===PHPExcel_Calculation_Functions::VALUE()){return false;}$dateValueNew+=$timeValue;}return $dateValueNew;}public static function monthStringToNumber($month){$monthIndex=1;foreach(self::$monthNames as $shortMonthName=>$longMonthName){if(($month===$longMonthName)||($month===$shortMonthName)){return $monthIndex;}++$monthIndex;}return $month;}public static function dayStringToNumber($day){$strippedDayValue=(str_replace(self::$numberSuffixes,'',$day));if(is_numeric($strippedDayValue)){return(integer)$strippedDayValue;}return $day;}}