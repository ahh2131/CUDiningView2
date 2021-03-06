<?php

/**
 * BaseModelWithNumberInColumn
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $column_1
 * @property string $column2
 * @property string $column__3
 * 
 * @method string                  getColumn1()   Returns the current record's "column_1" value
 * @method string                  getColumn2()   Returns the current record's "column2" value
 * @method string                  getColumn3()   Returns the current record's "column__3" value
 * @method ModelWithNumberInColumn setColumn1()   Sets the current record's "column_1" value
 * @method ModelWithNumberInColumn setColumn2()   Sets the current record's "column2" value
 * @method ModelWithNumberInColumn setColumn3()   Sets the current record's "column__3" value
 * 
 * @package    symfony12
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseModelWithNumberInColumn extends myDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('model_with_number_in_column');
        $this->hasColumn('column_1', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('column2', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('column__3', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));

        $this->option('symfony', array(
             'form' => false,
             'filter' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}