<?php

class DateTimeBehavior extends CActiveRecordBehavior {

  public $dateTimeSQLFormat = 'Y-m-d H:i:s';
  public $dateTimeTextFormat = 'dd MMM yyyy';

  public function dateToSql($date) {
    if ($date == "")
      return "";

    return date('Y-m-d', CDateTimeParser::parse($date, $this->dateTimeTextFormat));
  }

  public function tstamp($date) {
    if ($date == "")
      return "";

    return CDateTimeParser::parse($date, $this->dateTimeTextFormat);
  }

  /*
   * The beforeSave event is raised before the record is saved to database.
   * Converts input dateformat into yiis dateformat (for example: 31.12.2013 to 2013-12-31) .
   */

  public function beforeSave($event) {

    // search for date/datetime columns. Convert it to dateformat used in database (Y-m-d)
    foreach ($event->sender->tableSchema->columns as $columnName => $column) {

      if (($column->dbType != 'date') and ($column->dbType != 'datetime'))
        continue;


      if (!strlen($event->sender->$columnName)) {
        $event->sender->$columnName = null;
        continue;
      }
      
      $event->sender->$columnName = date($this->dateTimeSQLFormat, CDateTimeParser::parse($event->sender->$columnName, $this->dateTimeTextFormat));

    }
    return true;
  }

  /*
   * This event is raised after the record is instantiated (loaded from database)
   * Converts yiis database dateformat to input dateformat (for example: 2013-12-31 to 31.12.2013) .
   */

  public function afterFind($event) {

    foreach ($event->sender->tableSchema->columns as $columnName => $column) {

      if (($column->dbType != 'date') and ($column->dbType != 'datetime'))
        continue;

      if (!strlen($event->sender->$columnName) || $event->sender->$columnName == "0000-00-00") {
        $event->sender->$columnName = null;
        continue;
      }

      $event->sender->$columnName = Yii::app()->dateFormatter->format($this->dateTimeTextFormat, strtotime($event->sender->$columnName));
    }
    return true;
  }

}
